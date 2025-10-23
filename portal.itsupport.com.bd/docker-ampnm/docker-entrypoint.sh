#!/bin/bash
set -e

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
/usr/local/bin/wait-for-it.sh db:3306 --timeout=60 --strict -- echo "MySQL is up!"

# Run database setup script
echo "Running database setup script..."
php /var/www/html/database_setup.php

# Check if license_setup.php needs to be run (if license key is not yet set)
# We'll check the app_settings table for 'app_license_key'
# This requires a direct PDO connection as functions.php might not be fully loaded yet.
echo "Checking application license status..."
LICENSE_KEY_SET=$(php -r "
    require_once '/var/www/html/config.php';
    try {
        \$pdo = getDbConnection();
        \$stmt = \$pdo->prepare('SELECT setting_value FROM app_settings WHERE setting_key = \"app_license_key\"');
        \$stmt->execute();
        echo \$stmt->fetchColumn() ? 'true' : 'false';
    } catch (PDOException \$e) {
        // If app_settings table doesn't exist yet, it means database_setup.php might not have fully completed.
        // In this case, we assume license is not set.
        echo 'false';
    }
")

if [ "$LICENSE_KEY_SET" = "false" ]; then
    echo "Application license key not found. Redirecting to license setup page."
    # We don't run license_setup.php directly here, as it's a web page.
    # The application's bootstrap will handle the redirect to license_setup.php if the key is missing.
else
    echo "Application license key is already configured."
fi

# Start Apache in the foreground
echo "Starting Apache..."
exec apache2-foreground