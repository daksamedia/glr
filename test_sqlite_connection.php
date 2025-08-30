<?php
// Set environment variables to use SQLite
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE=' . __DIR__ . '/database/database.sqlite');

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Testing SQLite connection with Laravel...\n";
    $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "SQLite connection successful!\n";
    
    // Try to run a simple query
    $results = \Illuminate\Support\Facades\DB::select('SELECT 1 as test');
    echo "Query executed successfully: " . json_encode($results) . "\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
