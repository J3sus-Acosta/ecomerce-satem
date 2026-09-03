# Use official WordPress base image
FROM wordpress:latest

# Copy SATEM child theme and core plugin to /usr/src/wordpress (image seed)
COPY themes/satem-child /usr/src/wordpress/wp-content/themes/satem-child
COPY plugins/satem-core /usr/src/wordpress/wp-content/plugins/satem-core

# Copy custom entrypoint script to sync into persistent volume at runtime startup
COPY docker-entrypoint-satem.sh /usr/local/bin/docker-entrypoint-satem.sh
RUN chmod +x /usr/local/bin/docker-entrypoint-satem.sh

ENTRYPOINT ["docker-entrypoint-satem.sh"]
CMD ["apache2-foreground"]
