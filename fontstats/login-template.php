<?php
// Template only.
// Copy this to login.php on the real server and fill in the real values.
// Do not commit the real server file.

$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbUser = getenv('DB_USER') ?: 'your_db_user';
$dbPass = getenv('DB_PASSWORD') ?: 'your_db_password';
$dbName = getenv('DB_NAME') ?: 'your_database_name';

function hash_password(string $plainPassword): string
{
    return password_hash($plainPassword, PASSWORD_ARGON2ID);
}

function verify_password(string $plainPassword, string $storedHash): bool
{
    return password_verify($plainPassword, $storedHash);
}
