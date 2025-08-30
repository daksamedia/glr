<?php
echo "Available PDO drivers: " . implode(', ', PDO::getAvailableDrivers()) . "\n";

// Test SQLite connection directly
try {
    $pdo = new PDO('sqlite:' . __DIR__ . '/database/database.sqlite');
    echo "SQLite connection successful!\n";
} catch (PDOException $e) {
    echo "SQLite error: " . $e->getMessage() . "\n";
}

// Test MySQL connection (this should fail)
try {
    $pdo = new PDO('mysql:host=localhost;dbname=test', 'root', '');
    echo "MySQL connection successful!\n";
} catch (PDOException $e) {
    echo "MySQL error: " . $e->getMessage() . "\n";
}
