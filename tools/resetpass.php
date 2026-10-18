<?php

/**
 * CLI tool to reset a moderator password.
 *
 * Usage:
 *   php reset-password.php <username>
 *
 * Example:
 *   php reset-password.php admin
 */

// Don't allow this to be executed through a web server.
if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script must be run from the command line.\n");
    exit(1);
}
chdir("../");
// Load the application.
require 'inc/bootstrap.php';

if ($argc !== 2) {
    fwrite(STDERR, "Usage: php reset-password.php <username>\n");
    exit(1);
}

$username = $argv[1];

/*
 * Find the moderator account.
 */
$query = prepare('SELECT `id`, `username` FROM ``mods`` WHERE `username` = :username LIMIT 1');
$query->bindValue(':username', $username);
$query->execute() or error(db_error($query));

$user = $query->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    fwrite(STDERR, "User not found: {$username}\n");
    exit(1);
}

/*
 * Ask for the new password without echoing it.
 */
fwrite(STDOUT, "New password for {$user['username']}: ");

if (function_exists('shell_exec')) {
    // Disable terminal echo while reading the password.
    shell_exec('stty -echo');
}

$password = trim(fgets(STDIN));

if (function_exists('shell_exec')) {
    shell_exec('stty echo');
}

fwrite(STDOUT, PHP_EOL);

if ($password === '') {
    fwrite(STDERR, "Password cannot be empty.\n");
    exit(1);
}

/*
 * Confirm the password.
 */
fwrite(STDOUT, "Confirm password: ");

if (function_exists('shell_exec')) {
    shell_exec('stty -echo');
}

$confirmation = trim(fgets(STDIN));

if (function_exists('shell_exec')) {
    shell_exec('stty echo');
}

fwrite(STDOUT, PHP_EOL);

if ($password !== $confirmation) {
    fwrite(STDERR, "Passwords do not match.\n");
    exit(1);
}

/*
 * Use the application's password hashing implementation.
 */
list($version, $hash) = crypt_password($password);

$query = prepare(
    'UPDATE ``mods``
     SET `password` = :password, `version` = :version
     WHERE `id` = :id'
);

$query->bindValue(':id', $user['id']);
$query->bindValue(':password', $hash);
$query->bindValue(':version', $version);
$query->execute() or error(db_error($query));

echo "Password successfully reset for {$user['username']} (#{$user['id']}).\n";

