<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/workflow.php';

$conn = db_connect();
$user = require_role($conn, ['supervisor', 'role_manager', 'it_admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /src/approver.php');
    exit;
}
check_csrf();

$error = act_on_form($conn, $user, (int) ($_POST['formId'] ?? 0), (string) ($_POST['action'] ?? ''), (string) ($_POST['comment'] ?? ''));
header('Location: /src/approver.php?' . ($error ? 'error=' . urlencode($error) : 'done=' . urlencode($_POST['action'])));
