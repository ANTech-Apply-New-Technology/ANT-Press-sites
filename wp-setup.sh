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

    if ! wp core is-installed --path=/var/www/html --allow-root 2>/dev/null; then
        WP_URL="${WP_HOME:-http://localhost}"
        WP_TITLE="${WP_SITE_TITLE:-ANT-Press Site}"
        WP_ADMIN="${WP_ADMIN_USER:-admin}"
        WP_PASS="${WP_ADMIN_PASS:-$(head -c 24 /dev/urandom | base64)}"
        WP_EMAIL="${WP_ADMIN_EMAIL:-admin@antpress.dev}"

        wp core install             --path=/var/www/html             --url="$WP_URL"             --title="$WP_TITLE"             --admin_user="$WP_ADMIN"             --admin_password="$WP_PASS"             --admin_email="$WP_EMAIL"             --skip-email             --allow-root

        wp rewrite structure "/%postname%/" --path=/var/www/html --allow-root
        wp rewrite flush --path=/var/www/html --allow-root

        # Force-enable application passwords
        echo "<?php add_filter('wp_is_application_passwords_available', '__return_true');" > /var/www/html/wp-content/mu-plugins/force-app-passwords.php
        echo "WP-SETUP: mu-plugin created"
        # wp eval "add_filter(chr(39).chr(119).chr(112).chr(95).chr(105).chr(115).chr(95).chr(97).chr(112).chr(112).chr(108).chr(105).chr(99).chr(97).chr(116).chr(105).chr(111).chr(110).chr(95).chr(112).chr(97).chr(115).chr(115).chr(119).chr(111).chr(114).chr(100).chr(115).chr(95).chr(97).chr(118).chr(97).chr(105).chr(108).chr(97).chr(98).chr(108).chr(101).chr(39), chr(39).chr(95).chr(95).chr(114).chr(101).chr(116).chr(117).chr(114).chr(110).chr(95).chr(116).chr(114).chr(117).chr(101).chr(39));"             --path=/var/www/html --allow-root 2>/dev/null || true

        mkdir -p /var/www/html/wp-content/mu-plugins

        # Install Makiro Theme
        if [ -d /var/www/html/wp-content/themes/makiro-theme ]; then
            echo "WP-SETUP: Makiro theme found, activating..."
            wp theme activate makiro-theme --path=/var/www/html --allow-root
        fi

        APP_PASSWORD=$(wp user application-password create "$WP_ADMIN" "ant-press-api"             --path=/var/www/html --porcelain --allow-root 2>/dev/null || echo "")

        echo "==========================================" 
        echo "ANT-PRESS SETUP COMPLETE"
        echo "URL: $WP_URL"
        echo "Admin: $WP_ADMIN / $WP_PASS"
        echo "API App Password: $APP_PASSWORD"
        echo "==========================================" 
    else
        echo "WP-SETUP: WordPress already installed, skipping."
        if [ -d /var/www/html/wp-content/themes/makiro-theme ]; then
            CURRENT=$(wp theme list --status=active --field=name --path=/var/www/html --allow-root 2>/dev/null || echo "")
            if [ "$CURRENT" != "makiro-theme" ]; then
                echo "WP-SETUP: Activating Makiro theme..."
                wp theme activate makiro-theme --path=/var/www/html --allow-root
            fi
        fi
    fi
) &

exec docker-entrypoint.sh apache2-foreground
