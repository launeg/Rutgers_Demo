<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/view.php';

$conn = db_connect();
$token = csrf_token();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $user = find_user($conn, (int) ($_POST['userId'] ?? 0));
    if ($user) {
        sign_in($user['userID']);
        header('Location: ' . home_for_role($user['userRole']));
        exit;
    }
}

page_header('Sign in', null, 'form');
echo '<form method="post"><div class="user-information">';
echo '<p class="login-note">Demo Sign-in Page</p>';
echo '<input type="hidden" name="csrf" value="' . h($token) . '">';
echo '<div class="drop-container"><label>User</label><select name="userId">';
foreach (list_users($conn) as $u) {
    echo '<option value="' . (int) $u['userID'] . '">' . h($u['firstName'] . ' ' . $u['lastName'] . ' (' . $u['userRole'] . ')') . '</option>';
}
echo '</select></div><br><button type="submit">Sign in</button></div></form>';
page_footer();
