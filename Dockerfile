# Use official WordPress base image
FROM wordpress:latest

# Create target directories in both /usr/src/wordpress (image seed) and /var/www/html (runtime root)
RUN mkdir -p /usr/src/wordpress/wp-content/themes/satem-child \
    && mkdir -p /usr/src/wordpress/wp-content/plugins/satem-core \
    && mkdir -p /var/www/html/wp-content/themes/satem-child \
    && mkdir -p /var/www/html/wp-content/plugins/satem-core

# Copy SATEM child theme
COPY themes/satem-child /usr/src/wordpress/wp-content/themes/satem-child
COPY themes/satem-child /var/www/html/wp-content/themes/satem-child

# Copy SATEM core plugin
COPY plugins/satem-core /usr/src/wordpress/wp-content/plugins/satem-core
COPY plugins/satem-core /var/www/html/wp-content/plugins/satem-core

# Set proper web server permissions
RUN chown -R www-data:www-data /usr/src/wordpress/wp-content/themes/satem-child /usr/src/wordpress/wp-content/plugins/satem-core \
    && chown -R www-data:www-data /var/www/html/wp-content/themes/satem-child /var/www/html/wp-content/plugins/satem-core \
    && chmod -R 755 /usr/src/wordpress/wp-content/themes/satem-child /usr/src/wordpress/wp-content/plugins/satem-core \
    && chmod -R 755 /var/www/html/wp-content/themes/satem-child /var/www/html/wp-content/plugins/satem-core
