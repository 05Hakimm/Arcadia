#!/bin/bash
set -e

echo "Fixing Apache MPM configuration..."

# Remove any existing MPM config to be absolutely sure
rm -f /etc/apache2/mods-enabled/mpm_*.load
rm -f /etc/apache2/mods-enabled/mpm_*.conf

# Enable prefork explicitly
a2enmod mpm_prefork

echo "Starting Apache..."
exec docker-php-entrypoint apache2-foreground
