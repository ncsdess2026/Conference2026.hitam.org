<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'nc_sdess_2026');

// Email Configuration
define('ADMIN_EMAIL', 'ncsdess2026@hitam.org');
define('SENDER_EMAIL', 'ncsdess2026@hitam.org');
define('SENDER_NAME', 'NC-SDESS 2026 Conference');

// Establish Database Connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

?>
