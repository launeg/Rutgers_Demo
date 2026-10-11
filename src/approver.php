<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/view.php';

$conn = db_connect();
$user = require_role($conn, ['supervisor', 'role_manager', 'it_admin']);

$token = csrf_token();

page_header('Approval queue', $user);
if (isset($_GET['done'])) {
    echo '<p class="notice">Request updated.</p>';
}
if (isset($_GET['error'])) {
    echo '<p class="error">' . h($_GET['error']) . '</p>';
}
render_form_table(approval_queue($conn, $user), $token);
page_footer();
