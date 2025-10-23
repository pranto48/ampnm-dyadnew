#!/bin/bash
set -e

# Wait for MySQL to be ready
echo "Waiting for MySQL to start..."
/usr/local/bin/wait-for-it.sh db:3306 --timeout=60 --strict -- echo "MySQL is up!"

# Run database setup script
echo "Running database setup script..."
php /var/www/html/database_setup.php

# Start Apache in the foreground
echo "Starting Apache..."
exec apache2-foreground