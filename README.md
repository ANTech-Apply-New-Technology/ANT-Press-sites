# ANT-Press-sites

WordPress instances for ANT-Press. Each PR branch = one WP site environment on Railway.

## How it works

1. This repo is linked to a Railway project
2. The `main` branch has the base WordPress + MySQL setup
3. To provision a new WP site: create a PR branch → Railway auto-creates an environment
4. To destroy: close the PR → Railway auto-cleans up the environment

## Environment variables (set per Railway environment)

- `WORDPRESS_DB_HOST` — MySQL host (auto from Railway MySQL service)
- `WORDPRESS_DB_USER` — MySQL user
- `WORDPRESS_DB_PASSWORD` — MySQL password
- `WORDPRESS_DB_NAME` — MySQL database name
- `WP_HOME` — Site URL (auto from Railway domain)
- `WP_SITEURL` — Same as WP_HOME
- `WP_SITE_TITLE` — Site title
- `WP_ADMIN_USER` — Admin username (default: admin)
- `WP_ADMIN_EMAIL` — Admin email
