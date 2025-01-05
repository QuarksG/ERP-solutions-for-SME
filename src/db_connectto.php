<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database configuration
$host = 'localhost'; // Database server (typically localhost)
$dbName = 'u888950461_NewDS'; // The database name
$username = 'u888950461_Huseyin'; // The database username
$password = 'Yakup123*'; // The database password
$charset = 'utf8mb4'; // Charset

// DSN (Data Source Name) string for connecting to MySQL using PDO
$dsn = "mysql:host=$host;dbname=$dbName;charset=$charset";

// PDO options for better error handling and security
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throws exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetches results as associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Disables emulated prepared statements
];

try {
    // Create a new PDO instance and establish a database connection
    $pdo = new PDO($dsn, $username, $password, $options);
    return $pdo; // Ensure that the PDO connection is returned
} catch (PDOException $e) {
    // Handle connection errors gracefully
    die('Database connection failed: ' . $e->getMessage());
}
