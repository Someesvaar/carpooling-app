<?php
// Database connection using environment variables for Render / cloud deployment

$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$name = getenv('DB_NAME') ?: 'carpool_db';
$port = getenv('DB_PORT') ?: 3306;

$conn = new mysqli($host, $user, $pass, $name, (int)$port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
