<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

try {
    echo "Testing SQLite connection...\n";
    $pdo = \Illuminate\Support\Facades\DB::connection('sqlite')->getPdo();
    echo "SQLite connection successful!\n";
    
    echo "Testing MySQL connection...\n";
    $pdo = \Illuminate\Support\Facades\DB::connection('mysql')->getPdo();
    echo "MySQL connection successful!\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Available PDO drivers: " . implode(', ', \PDO::getAvailableDrivers()) . "\n";
}
