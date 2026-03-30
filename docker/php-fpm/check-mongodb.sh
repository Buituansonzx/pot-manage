#!/bin/bash

# Check PHP MongoDB Extension Script
echo "=== PHP MongoDB Extension Check ==="

# Check if MongoDB extension is loaded
if php -m | grep -q mongodb; then
    echo "✅ MongoDB extension is loaded"
    php -r "echo 'MongoDB extension version: ' . phpversion('mongodb') . PHP_EOL;"
else
    echo "❌ MongoDB extension is NOT loaded"
fi

# List all loaded extensions
echo ""
echo "=== All PHP Extensions ==="
php -m

# Test MongoDB connection (if extension is available)
if php -m | grep -q mongodb; then
    echo ""
    echo "=== Testing MongoDB Connection ==="
    php -r "
    // Try to load autoloader
    \$autoloaders = [
        '/root/.composer/vendor/autoload.php',
        '/var/www/html/homestay-api/vendor/autoload.php',
        '/usr/local/lib/composer/vendor/autoload.php'
    ];
    
    \$loaded = false;
    foreach (\$autoloaders as \$autoloader) {
        if (file_exists(\$autoloader)) {
            require_once \$autoloader;
            \$loaded = true;
            echo '✅ Loaded autoloader: ' . \$autoloader . PHP_EOL;
            break;
        }
    }
    
    if (!\$loaded) {
        echo '❌ MongoDB PHP Library not found. Install via: composer require mongodb/mongodb' . PHP_EOL;
        exit(1);
    }
    
    try {
        \$client = new MongoDB\Client('mongodb://admin:adminpassword@mongodb:27017');
        \$databases = \$client->listDatabases();
        echo '✅ MongoDB connection successful!' . PHP_EOL;
        echo 'Available databases:' . PHP_EOL;
        foreach (\$databases as \$db) {
            echo '  - ' . \$db['name'] . PHP_EOL;
        }
    } catch (Exception \$e) {
        echo '❌ MongoDB connection failed: ' . \$e->getMessage() . PHP_EOL;
    }
    "
fi