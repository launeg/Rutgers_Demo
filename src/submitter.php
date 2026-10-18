<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/view.php';

$conn = db_connect();
$user = require_role($conn, ['submitter']);
$all = forms_by_submitter($conn, $user['userID']);
$filtering = form_filter_sql($_GET)[0] !== '';
$forms = $filtering ? forms_by_submitter($conn, $user['userID'], $_GET) : $all;
$options = form_options($conn);

$counts = ['inProgress' => 0, 'approved' => 0, 'denied' => 0];
foreach ($all as $f) {
    $key = ['approved' => 'approved', 'denied' => 'denied'][$f['status']] ?? 'inProgress';
    $counts[$key]++;
}

/** A filter dropdown that keeps the current choice selected. Items are [id, label] pairs. */
function filter_select(string $name, string $label, array $items): void
{
    $current = (string) ($_GET[$name] ?? '');
    echo '<label>' . h($label) . '<select name="' . h($name) . '"><option value="">All</option>';
    foreach ($items as [$id, $text]) {
        $sel = $current === (string) $id ? ' selected' : '';
        echo '<option value="' . h((string) $id) . '"' . $sel . '>' . h($text) . '</option>';
    }
    echo '</select></label>';
}

page_header('My requests', $user);
if (isset($_GET['created'])) {
    echo '<p class="notice">Request submitted.</p>';
}
echo '<table class="table3"><tr><th>Forms In Progress</th><th>Forms Approved</th><th>Forms Denied</th></tr>'
    . '<tr><td>' . $counts['inProgress'] . '</td><td>' . $counts['approved'] . '</td><td>' . $counts['denied'] . '</td></tr></table><br>';

$pairs = fn (array $rows) => array_map(fn ($r) => [$r['id'], $r['label']], $rows);
echo '<form method="get" class="filters">';
echo '<label>Search<input type="text" name="q" value="' . h($_GET['q'] ?? '') . '" placeholder="Name, NetID or email"></label>';
filter_select('department', 'School/Department', $pairs($options['departments']));
filter_select('system', 'IT System', $pairs($options['systems']));

$currentRole = (string) ($_GET['role'] ?? '');
echo '<label>Role<select name="role"><option value="">All</option>';
$group = null;
foreach ($options['roles'] as $r) {
    if ($r['systemLabel'] !== $group) {
        echo ($group === null ? '' : '</optgroup>') . '<optgroup label="' . h($r['systemLabel']) . '">';
        $group = $r['systemLabel'];
    }
    $sel = $currentRole === (string) $r['id'] ? ' selected' : '';
    echo '<option value="' . (int) $r['id'] . '"' . $sel . '>' . h($r['label']) . '</option>';
}
echo '</optgroup></select></label>';

filter_select('status', 'Status', array_map(fn ($s) => [$s, status_label($s)], FORM_STATUSES));
echo '<button type="submit">Search</button><a href="/src/submitter.php" class="clear">Clear</a></form>';

if ($filtering && !$forms) {
    echo '<p class="summary">No requests match your search.</p>';
} else {
    render_form_table($forms);
}
page_footer();
