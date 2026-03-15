#!/usr/bin/env bash
set -euo pipefail

# This script runs as the CMD. It:
# 1. Fixes Apache MPM at runtime (in case build cache skipped it)
# 2. Lets the stock WordPress entrypoint set up wp-config.php and start Apache
# 3. Waits for WP to be ready in the background
# 4. Runs WP-CLI install + generates application password
# 5. Installs custom themes and mu-plugins from ANT-Press repo via git sparse checkout

# Fix MPM conflict at runtime — disable event, keep prefork (mod_php needs it)
a2dismod mpm_event 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true

# --- Theme and mu-plugin installation from ANT-Press repo ---
install_themes_and_mu_plugins() {
    local REPO_URL="${THEME_REPO_URL:-https://github.com/ANTech-Apply-New-Technology/ANT-Press.git}"
    local THEME_DIR="/var/www/html/wp-content/themes"
    local MU_PLUGIN_DIR="/var/www/html/wp-content/mu-plugins"
    local TEMP_DIR="/tmp/ant-press-assets"

    echo "WP-SETUP: Installing themes and mu-plugins from ANT-Press repo..."

    # Use GITHUB_TOKEN for private repo access if available
    local CLONE_URL="$REPO_URL"
    if [ -n "${GITHUB_TOKEN:-}" ]; then
        CLONE_URL="https://x-access-token:${GITHUB_TOKEN}@github.com/ANTech-Apply-New-Technology/ANT-Press.git"
    fi

    # Clean up any previous attempt
    rm -rf "$TEMP_DIR"

    # Sparse checkout — only clone docker/themes/ and docker/mu-plugins/ (minimal download)
    if git clone --depth 1 --filter=blob:none --sparse "$CLONE_URL" "$TEMP_DIR" 2>/dev/null; then
        cd "$TEMP_DIR"
        git sparse-checkout set docker/themes docker/mu-plugins 2>/dev/null

        if [ -d "$TEMP_DIR/docker/themes" ]; then
            cp -r "$TEMP_DIR/docker/themes/"* "$THEME_DIR/" 2>/dev/null || true
            INSTALLED=$(ls "$TEMP_DIR/docker/themes/" 2>/dev/null | tr '\n' ', ')
            echo "WP-SETUP: Themes installed: $INSTALLED"
        else
            echo "WP-SETUP: WARNING — No themes found in repo" >&2
        fi

        # Install mu-plugins
        if [ -d "$TEMP_DIR/docker/mu-plugins" ]; then
            mkdir -p "$MU_PLUGIN_DIR"
            for plugin_file in "$TEMP_DIR/docker/mu-plugins/"*.php; do
                local filename
                filename=$(basename "$plugin_file")
                # Do not overwrite force-app-passwords.php (created separately)
                if [ "$filename" = "force-app-passwords.php" ]; then
                    echo "WP-SETUP: Skipping $filename (managed separately)"
                    continue
                fi
                cp "$plugin_file" "$MU_PLUGIN_DIR/$filename"
            done
            MU_INSTALLED=$(ls "$TEMP_DIR/docker/mu-plugins/"*.php 2>/dev/null | xargs -n1 basename | tr '\n' ', ')
            echo "WP-SETUP: mu-plugins installed: $MU_INSTALLED"
        else
            echo "WP-SETUP: WARNING — No mu-plugins found in repo" >&2
        fi

        rm -rf "$TEMP_DIR"
    else
        echo "WP-SETUP: WARNING — Could not clone theme repo, continuing without custom themes" >&2
    fi
}

# Start the background setup process
(
    # Wait for WordPress to respond
    MAX_TRIES=60
    TRIES=0
    until curl -sf http://localhost/ > /dev/null 2>&1; do
        TRIES=$((TRIES + 1))
        if [ "$TRIES" -ge "$MAX_TRIES" ]; then
            echo "WP-SETUP: WordPress not ready after $MAX_TRIES attempts" >&2
            exit 1
        fi
        sleep 3
    done

    echo "WP-SETUP: WordPress is responding, running setup..."

    # Install WordPress if not already installed
    if ! wp core is-installed --path=/var/www/html --allow-root 2>/dev/null; then
        WP_URL="${WP_HOME:-http://localhost}"
        WP_TITLE="${WP_SITE_TITLE:-ANT-Press Site}"
        WP_ADMIN="${WP_ADMIN_USER:-admin}"
        WP_PASS="${WP_ADMIN_PASS:-$(head -c 24 /dev/urandom | base64)}"
        WP_EMAIL="${WP_ADMIN_EMAIL:-admin@antpress.dev}"

        wp core install \
            --path=/var/www/html \
            --url="$WP_URL" \
            --title="$WP_TITLE" \
            --admin_user="$WP_ADMIN" \
            --admin_password="$WP_PASS" \
            --admin_email="$WP_EMAIL" \
            --skip-email \
            --allow-root

        # Set permalink structure
        wp rewrite structure '/%postname%/' --path=/var/www/html --allow-root
        wp rewrite flush --path=/var/www/html --allow-root

        # Force-enable application passwords (required for HTTP/non-SSL environments)
        wp eval "add_filter('wp_is_application_passwords_available', '__return_true');" \
            --path=/var/www/html --allow-root 2>/dev/null || true

        # Add must-use plugin to persistently enable app passwords on HTTP
        mkdir -p /var/www/html/wp-content/mu-plugins
        cat > /var/www/html/wp-content/mu-plugins/force-app-passwords.php <<'MUEOF'
<?php
// Force-enable application passwords on non-SSL (for Railway/dev environments)
add_filter('wp_is_application_passwords_available', '__return_true');
MUEOF

        # Generate application password for REST API access
        APP_PASSWORD=$(wp user application-password create "$WP_ADMIN" "ant-press-api" \
            --path=/var/www/html --porcelain --allow-root 2>/dev/null || echo "")

        echo "=========================================="
        echo "ANT-PRESS SETUP COMPLETE"
        echo "URL: $WP_URL"
        echo "Admin: $WP_ADMIN / $WP_PASS"
        echo "API App Password: $APP_PASSWORD"
        echo "=========================================="
    else
        echo "WP-SETUP: WordPress already installed, skipping."
    fi

    # Install themes and mu-plugins (runs for BOTH fresh installs and existing sites)
    install_themes_and_mu_plugins

) &

# Hand off to the stock WordPress entrypoint (sets up wp-config.php + starts Apache)
exec docker-entrypoint.sh apache2-foreground
