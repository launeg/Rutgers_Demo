<?php
/**
 * Central configuration: reads settings from the process environment, falling
 * back to an optional `.env` file in the project root. No credentials live in
 * source control.
 */

/**
 * Parse a minimal KEY=VALUE .env file. Existing environment variables win.
 */
function load_env_file(string $path): void
{
    if (!is_readable($path)) {
        return;
    }
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
            continue;
        }
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $value = trim($value, "\"'");
        if (getenv($key) === false) {
            putenv("$key=$value");
        }
    }
}

/**
 * Read a configuration value from the environment.
 *
 * @param string      $key     Variable name, e.g. DB_HOST
 * @param string|null $default Returned when the variable is unset
 */
function env(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    return $value === false ? $default : $value;
}

/**
 * True when APP_ENV=development. Gates verbose error output.
 */
function is_debug(): bool
{
    return env('APP_ENV', 'production') === 'development';
}

/**
 * Open a MySQL connection using DB_* settings. Throws on failure.
 *
 * @throws RuntimeException if a required setting is missing or the connection fails
 */
function db_connect(): mysqli
{
    foreach (['DB_HOST', 'DB_NAME', 'DB_USER'] as $required) {
        if (env($required) === null) {
            throw new RuntimeException("Missing required configuration: $required (see .env.example)");
        }
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = new mysqli(
        env('DB_HOST'),
        env('DB_USER'),
        env('DB_PASS', ''),
        env('DB_NAME'),
        (int) env('DB_PORT', '3306')
    );
    $conn->set_charset('utf8mb4');
    return $conn;
}

load_env_file(__DIR__ . '/../.env');
