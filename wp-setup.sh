#!/usr/bin/env bash
set -euo pipefail

# Wait for MySQL to be ready
MAX_TRIES=30
TRIES=0
until mysqladmin ping -h"${WORDPRESS_DB_HOST:-db}" --silent 2>/dev/null; do
    TRIES=$((TRIES + 1))
    if [ "$TRIES" -ge "$MAX_TRIES" ]; then
        echo "ERROR: MySQL not ready after $MAX_TRIES attempts" >&2
        exit 1
    fi
    echo "Waiting for MySQL... ($TRIES/$MAX_TRIES)"
    sleep 2
done

# Run the default WordPress entrypoint to set up wp-config.php
docker-entrypoint.sh apache2 -v > /dev/null 2>&1 || true

# Install WordPress if not already installed
if ! sudo -u www-data wp core is-installed --path=/var/www/html 2>/dev/null; then
    WP_URL="${WP_HOME:-http://localhost}"
    WP_TITLE="${WP_SITE_TITLE:-ANT-Press Site}"
    WP_ADMIN_USER="${WP_ADMIN_USER:-admin}"
    WP_ADMIN_PASS="${WP_ADMIN_PASS:-$(openssl rand -base64 16)}"
    WP_ADMIN_EMAIL="${WP_ADMIN_EMAIL:-admin@antpress.local}"

    sudo -u www-data wp core install \
        --path=/var/www/html \
        --url="$WP_URL" \
        --title="$WP_TITLE" \
        --admin_user="$WP_ADMIN_USER" \
        --admin_password="$WP_ADMIN_PASS" \
        --admin_email="$WP_ADMIN_EMAIL" \
        --skip-email

    # Set permalink structure
    sudo -u www-data wp rewrite structure '/%postname%/' --path=/var/www/html

    # Generate application password for REST API access
    APP_PASSWORD=$(sudo -u www-data wp user application-password create "$WP_ADMIN_USER" "ant-press-api" --path=/var/www/html --porcelain 2>/dev/null || echo "")

    if [ -n "$APP_PASSWORD" ]; then
        echo "=========================================="
        echo "ANT-PRESS SETUP COMPLETE"
        echo "URL: $WP_URL"
        echo "Admin: $WP_ADMIN_USER"
        echo "Admin Password: $WP_ADMIN_PASS"
        echo "API App Password: $APP_PASSWORD"
        echo "=========================================="

        # Write credentials to a file the backend can read via a callback
        cat > /tmp/ant-press-credentials.json <<EOF
{
    "wp_url": "$WP_URL",
    "wp_username": "$WP_ADMIN_USER",
    "wp_admin_password": "$WP_ADMIN_PASS",
    "wp_app_password": "$APP_PASSWORD"
}
EOF
    fi
fi

# Hand off to Apache
exec apache2-foreground
