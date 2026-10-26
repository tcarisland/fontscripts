<?php
// Template only.
// Copy this file to login.php on the real server and fill in the real values.
// Do not commit the real server file.

$servername = "hander01.mysql.domeneshop.no";
$username = "hander01";
$dbname = "hander01";

// The real DB password must be supplied by the environment, not stored in the repo.
// On the server: export DB_PASSWORD="your-real-db-password"
$password = getenv('DB_PASSWORD');

if ($password === false || $password === '') {
    throw new RuntimeException('DB_PASSWORD is not set.');
}
