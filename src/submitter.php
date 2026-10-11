<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/view.php';

$conn = db_connect();
$user = require_role($conn, ['submitter']);
$forms = forms_by_submitter($conn, $user['userID']);

$counts = ['inProgress' => 0, 'approved' => 0, 'denied' => 0];
foreach ($forms as $f) {
    $key = ['approved' => 'approved', 'denied' => 'denied'][$f['status']] ?? 'inProgress';
    $counts[$key]++;
}

page_header('My requests', $user);
if (isset($_GET['created'])) {
    echo '<p class="notice">Request submitted.</p>';
}
echo '<table class="table3"><tr><th>Forms In Progress</th><th>Forms Approved</th><th>Forms Denied</th></tr>'
    . '<tr><td>' . $counts['inProgress'] . '</td><td>' . $counts['approved'] . '</td><td>' . $counts['denied'] . '</td></tr></table><br>';
render_form_table($forms);
page_footer();
