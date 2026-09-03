# Use official WordPress base image
FROM wordpress:latest

# Copy SATEM child theme into wp-content/themes/satem-child
COPY themes/satem-child /var/www/html/wp-content/themes/satem-child

# Copy SATEM core plugin into wp-content/plugins/satem-core
COPY plugins/satem-core /var/www/html/wp-content/plugins/satem-core

# Set proper web server permissions
RUN chown -R www-data:www-data /var/www/html/wp-content/themes/satem-child \
    && chown -R www-data:www-data /var/www/html/wp-content/plugins/satem-core \
    && chmod -R 755 /var/www/html/wp-content/themes/satem-child \
    && chmod -R 755 /var/www/html/wp-content/plugins/satem-core
