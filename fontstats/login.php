<?php
// This file is a template ONLY.
// Create a real login.php on the server with the actual DB settings.
// That real file should be kept out of version control.

$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbUser = getenv('DB_USER') ?: 'your_db_user';
$dbPass = getenv('DB_PASSWORD') ?: 'your_db_password';
$dbName = getenv('DB_NAME') ?: 'your_database_name';
