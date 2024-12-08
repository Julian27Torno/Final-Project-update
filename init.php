<?php

use App\Models\DatabaseConnection;

// Initialize Templating Engine
try {
    Mustache_Autoloader::register();
    $mustache = new Mustache_Engine(array(
        'loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/views')
    ));
} catch (Exception $e) {
    error_log('Failed to initialize templating engine: ' . $e->getMessage());
    die('A critical error occurred. Please try again later.');
}

// Load Environment Variables
try {
    $dotenv = Dotenv\Dotenv::createMutable(__DIR__);
    $dotenv->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    error_log('Environment file not found: ' . $e->getMessage());
    die('Configuration error. Please contact support.');
}

// Initialize Database Connection
try {
    $db_type = $_ENV['DB_CONNECTION'] ?? 'mysql'; // Default to MySQL if not set
    $db_host = $_ENV['DB_HOST'] ?? '127.0.0.1';
    $db_port = $_ENV['DB_PORT'] ?? '3306';
    $db_name = $_ENV['DB_DATABASE'] ?? '';
    $db_username = $_ENV['DB_USERNAME'] ?? 'root';
    $db_password = $_ENV['DB_PASSWORD'] ?? '';

    if (!$db_name) {
        throw new Exception('Database name is not set in the environment variables.');
    }

    $db = new DatabaseConnection(
        $db_type,
        $db_host,
        $db_port,
        $db_name,
        $db_username,
        $db_password
    );
    $conn = $db->connect();
} catch (PDOException $e) {
    error_log('Database connection error: ' . $e->getMessage());
    die('Database connection failed. Please try again later.');
} catch (Exception $e) {
    error_log('General error during database connection: ' . $e->getMessage());
    die('A critical error occurred. Please try again later.');
}

// Additional Helper Functions
// Uncomment if needed
/*
function dump($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

function warn($data) {
    global $log;
    $log->warning($data);
}

function err($data) {
    global $log;
    $log->error($data);
}
*/
