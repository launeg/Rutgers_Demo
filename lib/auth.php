<?php
/**
 * Demo sign-in and CSRF helpers.
 * Sign-in picks a fictional seeded user. It stands in for campus single sign-on
 * and is not real authentication.
 */
require_once __DIR__ . '/data.php';

function start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Lax']);
    }
}

function current_user(mysqli $conn): ?array
{
    start_session();
    return empty($_SESSION['user_id']) ? null : find_user($conn, (int) $_SESSION['user_id']);
}

/** Return the signed-in user or redirect to the sign-in page. */
function require_login(mysqli $conn): array
{
    $user = current_user($conn);
    if (!$user) {
        header('Location: /src/login.php');
        exit;
    }
    return $user;
}

/** Return the signed-in user, or send 403 if their role is not allowed. */
function require_role(mysqli $conn, array $roles): array
{
    $user = require_login($conn);
    if (!in_array($user['userRole'], $roles, true)) {
        http_response_code(403);
        exit('Forbidden');
    }
    return $user;
}

function sign_in(int $userId): void
{
    start_session();
    session_regenerate_id(true);
    $_SESSION['user_id'] = $userId;
}

function sign_out(): void
{
    start_session();
    $_SESSION = [];
    session_destroy();
}

function home_for_role(string $role): string
{
    return queue_status_for_role($role) ? '/src/approver.php' : '/src/submitter.php';
}

function csrf_token(): string
{
    start_session();
    $_SESSION['csrf'] = $_SESSION['csrf'] ?? bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}

/** Stop the request if the posted token does not match the session. */
function check_csrf(): void
{
    start_session();
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(403);
        exit('Invalid form token');
    }
}
