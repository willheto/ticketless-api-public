#!/bin/bash

DATABASE_NAME="ticketlessLocal"

sudo mysql -u root --execute="CREATE DATABASE IF NOT EXISTS ${DATABASE_NAME};"

export APP_ENV=local
export QUEUE_CONNECTION=sync
php artisan migrate:fresh --seed

# Restart PHP process to clear caches
sudo service php8.1-fpm restart

# Exit with success
exit 0
