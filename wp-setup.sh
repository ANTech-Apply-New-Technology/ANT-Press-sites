#!/usr/bin/env bash
set -euo pipefail

(
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

        wp rewrite structure '/%postname%/' --path=/var/www/html --allow-root
        wp rewrite flush --path=/var/www/html --allow-root

        wp eval "add_filter('wp_is_application_passwords_available', '__return_true');" \
            --path=/var/www/html --allow-root 2>/dev/null || true

        mkdir -p /var/www/html/wp-content/mu-plugins
        cat > /var/www/html/wp-content/mu-plugins/force-app-passwords.php <<'MUEOF'
<?php
add_filter('wp_is_application_passwords_available', '__return_true');
MUEOF

        # Activate nimbus-theme
        wp theme activate nimbus-theme --path=/var/www/html --allow-root 2>/dev/null || \
            echo "WP-SETUP: nimbus-theme activation failed, using default"

        APP_PASSWORD=$(wp user application-password create "$WP_ADMIN" "ant-press-api" \
            --path=/var/www/html --porcelain --allow-root 2>/dev/null || echo "")

        echo "=========================================="
        echo "ANT-PRESS SETUP COMPLETE"
        echo "URL: $WP_URL"
        echo "Admin: $WP_ADMIN / $WP_PASS"
        echo "API App Password: $APP_PASSWORD"
        echo "=========================================="
    else
        # Activate nimbus-theme even on existing installs
        wp theme activate nimbus-theme --path=/var/www/html --allow-root 2>/dev/null || true
        echo "WP-SETUP: WordPress already installed, nimbus-theme activated."
    fi
) &

exec docker-entrypoint.sh apache2-foreground
