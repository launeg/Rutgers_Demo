<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/view.php';

$conn = db_connect();
$user = require_role($conn, ['supervisor', 'role_manager', 'it_admin']);

page_header('Approval queue', $user);
render_form_table(approval_queue($conn, $user));
page_footer();
