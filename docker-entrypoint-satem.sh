#!/bin/bash
set -e

# Sync satem-child theme into persistent volume at runtime startup
if [ -d "/usr/src/wordpress/wp-content/themes/satem-child" ]; then
    mkdir -p /var/www/html/wp-content/themes/satem-child
    cp -rf /usr/src/wordpress/wp-content/themes/satem-child/* /var/www/html/wp-content/themes/satem-child/
    chown -R www-data:www-data /var/www/html/wp-content/themes/satem-child
    chmod -R 755 /var/www/html/wp-content/themes/satem-child
fi

# Sync satem-core plugin into persistent volume at runtime startup
if [ -d "/usr/src/wordpress/wp-content/plugins/satem-core" ]; then
    mkdir -p /var/www/html/wp-content/plugins/satem-core
    cp -rf /usr/src/wordpress/wp-content/plugins/satem-core/* /var/www/html/wp-content/plugins/satem-core/
    chown -R www-data:www-data /var/www/html/wp-content/plugins/satem-core
    chmod -R 755 /var/www/html/wp-content/plugins/satem-core
fi

# Execute original WordPress entrypoint script
exec docker-entrypoint.sh "$@"
