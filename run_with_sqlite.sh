#!/bin/bash
# Script to run Laravel application with SQLite database

# Set environment variables for SQLite
export DB_CONNECTION=sqlite
export DB_DATABASE=$(pwd)/database/database.sqlite

# Check if SQLite database file exists, create if not
if [ ! -f "$DB_DATABASE" ]; then
    echo "Creating SQLite database file..."
    touch "$DB_DATABASE"
    chmod 755 "$DB_DATABASE"
fi

# Run Laravel migrations
echo "Running migrations..."
php artisan migrate --force

# Start the development server
echo "Starting Laravel development server..."
php artisan serve --host=0.0.0.0 --port=8000
