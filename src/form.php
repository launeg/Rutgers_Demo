<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/view.php';

$conn = db_connect();
$user = require_role($conn, ['submitter']);
$token = csrf_token();
$options = form_options($conn);
$errors = [];
$in = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $in = $_POST;
    $errors = validate_form($in, $options);
    if (!$errors) {
        create_form($conn, $user['userID'], $in);
        header('Location: /src/submitter.php?created=1');
        exit;
    }
}

function error_for(string $name, array $errors): void
{
    if (isset($errors[$name])) {
        echo '<p class="error">' . h($errors[$name]) . '</p>';
    }
}

function field(string $name, string $label, array $in, array $errors, string $type = 'text'): void
{
    echo '<div class="input-container"><label>' . h($label) . '</label>'
        . '<input type="' . h($type) . '" name="' . h($name) . '" value="' . h($in[$name] ?? '') . '"></div>';
    error_for($name, $errors);
}

function dropdown(string $name, string $label, array $items, array $in, array $errors): void
{
    echo '<div class="drop-container"><label>' . h($label) . '</label><select name="' . h($name) . '"><option value=""></option>';
    foreach ($items as $i) {
        $sel = (string) ($in[$name] ?? '') === (string) $i['id'] ? ' selected' : '';
        echo '<option value="' . (int) $i['id'] . '"' . $sel . '>' . h($i['label']) . '</option>';
    }
    echo '</select></div>';
    error_for($name, $errors);
}

function radios(string $name, array $items, array $in, string $class): void
{
    foreach ($items as $i) {
        $chk = (string) ($in[$name] ?? '') === (string) $i['id'] ? ' checked' : '';
        echo '<div class="' . h($class) . '"><input type="radio" id="' . h($name . $i['id']) . '" name="' . h($name) . '" value="'
            . (int) $i['id'] . '"' . $chk . '><label for="' . h($name . $i['id']) . '">' . h($i['label']) . '</label></div>';
    }
}

page_header('SASN IT Access Request Form', $user, 'form');
echo '<form method="post"><input type="hidden" name="csrf" value="' . h($token) . '">';

echo '<div class="user-information"><h3>USER INFORMATION</h3>';
field('firstName', 'First Name', $in, $errors);
field('lastName', 'Last Name', $in, $errors);
field('netid', 'NetID', $in, $errors);
echo '<br>';
field('email', 'Email', $in, $errors, 'email');
field('effDate', 'Eff Date', $in, $errors, 'date');
echo '<br>';
dropdown('departmentId', 'School and Department', $options['departments'], $in, $errors);
echo '</div><hr>';

echo '<div class="supervisor-information"><h3>SUPERVISOR INFORMATION</h3>';
dropdown('supervisorId', 'Supervisor', $options['supervisors'], $in, $errors);
echo '</div><hr>';

echo '<div class="request-information"><h3>REQUEST</h3>';
radios('requestTypeId', $options['requestTypes'], $in, 'request-container');
error_for('requestTypeId', $errors);
echo '</div><hr>';

echo '<div class="employee-information"><h3>EMPLOYEE</h3>';
radios('employeeTypeId', $options['employeeTypes'], $in, 'employee-container');
error_for('employeeTypeId', $errors);
echo '</div><hr>';

$bySystem = [];
foreach ($options['roles'] as $r) {
    $bySystem[$r['systemLabel']][] = $r;
}
$selected = array_map('strval', $in['roleIds'] ?? []);
echo '<div class="table-information"><table class="table-body"><tr><th>IT System</th><th>Roles</th></tr>';
foreach ($bySystem as $system => $roles) {
    echo '<tr><td>' . h($system) . '</td><td class="role-list">';
    foreach ($roles as $r) {
        $chk = in_array((string) $r['id'], $selected, true) ? ' checked' : '';
        echo '<label><input type="checkbox" name="roleIds[]" value="' . (int) $r['id'] . '"' . $chk . '> ' . h($r['label']) . '</label>';
    }
    echo '</td></tr>';
}
echo '</table>';
error_for('roleIds', $errors);
echo '</div><hr>';

echo '<div class="access-information"><h3>JUSTIFICATION FOR ACCESS REQUESTED</h3>'
    . '<textarea name="justification" rows="4">' . h($in['justification'] ?? '') . '</textarea>';
error_for('justification', $errors);
echo '</div><hr>';

echo '<div class="approval-information"><h3>APPROVAL</h3><b>Supervisor:</b>'
    . '<p>By submitting this form, I certify that the user requires access to data within the above system(s) to perform their job duties. '
    . 'I understand that it is my obligation to ensure that adequate training is provided to the user in compliance with state and federal laws, '
    . 'and University policies governing access to information contained in employee, applicant, and student records. '
    . 'I also certify that the person requesting access has read the "School of Arts and Sciences Confidentiality Agreement".</p>';
field('approvalName', 'Name', $in, $errors);
field('approvalSignature', 'Signature', $in, $errors);
field('approvalDate', 'Date', $in, $errors, 'date');
echo '</div><br><button type="submit" class="submit-button">Submit</button></form>';
page_footer();
