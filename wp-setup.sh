#!/usr/bin/env bash
set -euo pipefail

a2dismod mpm_event 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true

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

    WP_URL="${WP_HOME:-http://localhost}"
    WP_TITLE="${WP_SITE_TITLE:-ANT-Press Site}"
    WP_ADMIN="${WP_ADMIN_USER:-admin}"
    WP_PASS="${WP_ADMIN_PASS:-$(head -c 24 /dev/urandom | base64)}"
    WP_EMAIL="${WP_ADMIN_EMAIL:-admin@antpress.dev}"

    if ! wp core is-installed --path=/var/www/html --allow-root 2>/dev/null; then

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

        mkdir -p /var/www/html/wp-content/mu-plugins
        printf '%s\n' '<?php' 'add_filter("wp_is_application_passwords_available", "__return_true");' > /var/www/html/wp-content/mu-plugins/force-app-passwords.php

        # Activate Smuggler theme
        if [ -d /var/www/html/wp-content/themes/smuggler-theme ]; then
            echo "WP-SETUP: Activating Smuggler theme..."
            wp theme activate smuggler-theme --path=/var/www/html --allow-root
        fi

        APP_PASSWORD=$(wp user application-password create "$WP_ADMIN" "ant-press-api" \
            --path=/var/www/html --porcelain --allow-root 2>/dev/null || echo "")

        echo "=========================================="
        echo "ANT-PRESS SETUP COMPLETE"
        echo "URL: $WP_URL"
        echo "Admin: $WP_ADMIN / $WP_PASS"
        echo "API App Password: $APP_PASSWORD"
        echo "=========================================="
    else
        echo "WP-SETUP: WordPress already installed, skipping core install."
        # Still activate makiro if not active
        if [ -d /var/www/html/wp-content/themes/smuggler-theme ]; then
            CURRENT=$(wp theme list --status=active --field=name --path=/var/www/html --allow-root 2>/dev/null || echo "")
            if [ "$CURRENT" != "smuggler-theme" ]; then
                echo "WP-SETUP: Activating Smuggler theme..."
                wp theme activate smuggler-theme --path=/var/www/html --allow-root
            fi
        fi
    fi

    # Set JAMA Maskin theme customizer values
    # Smuggler is a block theme - content via REST API

exec docker-entrypoint.sh apache2-foreground
