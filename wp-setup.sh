#!/usr/bin/env bash
set -euo pipefail

# This script runs as the CMD. It:
# 1. Fixes Apache MPM at runtime (in case build cache skipped it)
# 2. Lets the stock WordPress entrypoint set up wp-config.php and start Apache
# 3. Waits for WP to be ready in the background
# 4. Runs WP-CLI install + generates application password
# 5. Installs custom themes and mu-plugins from ANT-Press repo via git sparse checkout
# 6. Conditionally installs WooCommerce when WC_ENABLED=true (ANT-693)

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
            echo "WP-SETUP: DEBUG — Theme directory contents:"
            ls -la "$THEME_DIR/" 2>&1
            echo "WP-SETUP: DEBUG — Theme dir owner: $(stat -c '%U:%G %a' "$THEME_DIR" 2>/dev/null)"
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
            echo "WP-SETUP: DEBUG — mu-plugins directory contents:"
            ls -la "$MU_PLUGIN_DIR/" 2>&1
            echo "WP-SETUP: DEBUG — mu-plugins dir owner: $(stat -c '%U:%G %a' "$MU_PLUGIN_DIR" 2>/dev/null)"
        else
            echo "WP-SETUP: WARNING — No mu-plugins found in repo" >&2
        fi

        rm -rf "$TEMP_DIR"
    else
        echo "WP-SETUP: WARNING — Could not clone theme repo, continuing without custom themes" >&2
    fi

    # Fix ownership so Apache/PHP (www-data) can read themes and mu-plugins
    chown -R www-data:www-data /var/www/html/wp-content/

    echo "WP-SETUP: DEBUG — Full wp-content structure:"
    find /var/www/html/wp-content -maxdepth 2 -type d 2>&1 || true
    echo "WP-SETUP: DEBUG — Process running as: $(whoami)"
}

# --- WooCommerce conditional installation (ANT-693) ---
install_woocommerce() {
    if [ "${WC_ENABLED:-false}" != "true" ]; then
        echo "WP-SETUP: WC_ENABLED is not true, skipping WooCommerce installation."
        return 0
    fi

    echo "WP-SETUP: WC_ENABLED=true — Installing WooCommerce..."

    # Install and activate WooCommerce
    wp plugin install woocommerce --activate --path=/var/www/html --allow-root

    echo "WP-SETUP: WooCommerce installed and activated."

    # Generate WooCommerce REST API consumer key/secret via WP-CLI
    # Uses wp eval to call the WC API key generation directly
    WC_KEYS=$(wp eval '
        if ( ! class_exists( "WooCommerce" ) ) {
            echo "ERROR:WooCommerce not loaded";
            return;
        }
        // Ensure WC tables exist
        if ( ! get_option( "woocommerce_db_version" ) ) {
            WC_Install::install();
        }
        global $wpdb;
        $consumer_key    = "ck_" . wc_rand_hash();
        $consumer_secret = "cs_" . wc_rand_hash();
        $data = array(
            "user_id"         => 1,
            "description"     => "ANT-Press API",
            "permissions"     => "read_write",
            "consumer_key"    => wc_api_hash( $consumer_key ),
            "consumer_secret" => $consumer_secret,
            "truncated_key"   => substr( $consumer_key, -7 ),
        );
        $wpdb->insert( $wpdb->prefix . "woocommerce_api_keys", $data );
        echo $consumer_key . " " . $consumer_secret;
    ' --path=/var/www/html --allow-root 2>/dev/null || echo "")

    if [ -n "$WC_KEYS" ] && [ "$WC_KEYS" != "ERROR:WooCommerce not loaded" ]; then
        WC_CONSUMER_KEY=$(echo "$WC_KEYS" | awk '{print $1}')
        WC_CONSUMER_SECRET=$(echo "$WC_KEYS" | awk '{print $2}')
        echo "WP-SETUP: WooCommerce API keys generated."
        echo "WC Consumer Key: $WC_CONSUMER_KEY"
        echo "WC Consumer Secret: $WC_CONSUMER_SECRET"
    else
        WC_CONSUMER_KEY=""
        WC_CONSUMER_SECRET=""
        echo "WP-SETUP: WARNING — Could not generate WooCommerce API keys" >&2
    fi

    echo "=========================================="
    echo "WOOCOMMERCE SETUP COMPLETE"
    echo "WC Consumer Key: ${WC_CONSUMER_KEY:-N/A}"
    echo "WC Consumer Secret: ${WC_CONSUMER_SECRET:-N/A}"
    echo "=========================================="
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

    # Install WooCommerce if WC_ENABLED=true (ANT-693)
    install_woocommerce

) &

# Hand off to the stock WordPress entrypoint (sets up wp-config.php + starts Apache)
exec docker-entrypoint.sh apache2-foreground
