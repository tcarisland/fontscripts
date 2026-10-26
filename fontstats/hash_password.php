#!/usr/bin/env php
<?php
if ($argc < 2) {
    fwrite(STDERR, "Usage: php hash_password.php 'your password'\n");
    exit(1);
}

$password = $argv[1];
$hash = password_hash($password, PASSWORD_ARGON2ID);

echo "Hash: " . $hash . PHP_EOL;
echo PHP_EOL;
echo "Paste this into login.php:\n";
echo "\$storedPasswordHash = '" . str_replace("'", "\\'", $hash) . "';\n";
