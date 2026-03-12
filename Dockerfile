FROM wordpress:6.7-php8.3-apache

# Fix MPM conflict (disable event, keep prefork which mod_php needs)
RUN a2dismod mpm_event 2>/dev/null; a2enmod mpm_prefork 2>/dev/null; true

# Install WP-CLI
RUN curl -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x /usr/local/bin/wp \
    && wp --info --allow-root

# Copy setup script
COPY wp-setup.sh /usr/local/bin/wp-setup.sh
RUN chmod +x /usr/local/bin/wp-setup.sh

EXPOSE 80

# Our script starts background WP setup, then execs into the stock entrypoint
CMD ["/usr/local/bin/wp-setup.sh"]
