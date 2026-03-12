FROM wordpress:6.9-php8.3-apache

# Install WP-CLI
RUN curl -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x /usr/local/bin/wp

# Copy setup script
COPY wp-setup.sh /usr/local/bin/wp-setup.sh
RUN chmod +x /usr/local/bin/wp-setup.sh

EXPOSE 80

# Run setup on container start, then hand off to default WordPress entrypoint
CMD ["/usr/local/bin/wp-setup.sh"]
