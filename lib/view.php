<?php
function h(?string $text): string
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

/** $sheet is 'form' for the request form and sign-in, 'main' for the tables. */
function page_header(string $title, ?array $user = null, string $sheet = 'main'): void
{
    echo '<!DOCTYPE html><html lang="en-US"><head><meta charset="utf-8">';
    echo '<title>' . h($title) . ' | SASN IT Access Requests</title>';
    echo '<link rel="stylesheet" href="/css/' . h($sheet) . '.css"><link rel="stylesheet" href="/css/app.css"></head><body>';
    echo '<header><div class="navbar"><img src="/images/logo.png" alt="Rutgers Logo" class="image1">'
        . '<img src="/images/sprite_backgrounds.png" alt="" class="image2"></div></header>';
    echo '<div class="demo-banner">DEMO ONLY: Not real personal data or SSO.</div>';
    echo '<div class="form-container">';
    if ($user) {
        echo '<ul class="tabs">';
        if ($user['userRole'] === 'submitter') {
            echo '<li class="tab"><a href="/src/submitter.php">My requests</a></li>'
                . '<li class="tab"><a href="/src/form.php">New request</a></li>';
        } else {
            echo '<li class="tab"><a href="/src/approver.php">Approval queue</a></li>';
        }
        echo '<li class="tab user">' . h($user['firstName'] . ' ' . $user['lastName']) . ' (' . h($user['userRole']) . ')</li>';
        echo '<li class="tab"><a href="/src/logout.php">Sign out</a></li></ul>';
    }
    echo '<h1>' . h($title) . '</h1>';
}

function page_footer(): void
{
    echo '</div></body></html>';
}

function status_label(string $status): string
{
    return ucfirst(str_replace('_', ' ', $status));
}

function action_form(int $formId, string $token): string
{
    return '<form method="post" action="/src/action.php" class="row-actions">'
        . '<input type="hidden" name="csrf" value="' . h($token) . '"><input type="hidden" name="formId" value="' . $formId . '">'
        . '<input type="text" name="comment" maxlength="500" placeholder="Comment (optional)">'
        . '<button name="action" value="approve">Sign</button> <button name="action" value="deny" class="reject">Reject</button></form>';
}

/** Table of requests, shared by the submitter and approver pages. Pass a CSRF token to add Sign/Reject buttons. */
function render_form_table(array $forms, ?string $token = null): void
{
    if (!$forms) {
        echo '<p class="summary">No requests.</p>';
        return;
    }
    echo '<div class="table-information"><table class="table2"><tr><th>Date Sub</th><th>Date Eff</th><th>Employee</th><th>NetID</th>'
        . '<th>Department</th><th>Request</th><th>Roles</th><th>Supervisor</th><th>Status</th>' . ($token ? '<th>Action</th>' : '') . '</tr>';
    foreach ($forms as $f) {
        echo '<tr><td>' . h(substr($f['entryDate'], 0, 10)) . '</td><td>' . h($f['effDate']) . '</td><td>' . h($f['employee'])
            . '</td><td>' . h($f['userNetID']) . '</td><td>' . h($f['department']) . '</td><td>' . h($f['requestType'])
            . '</td><td>' . h($f['roles']) . '</td><td>' . h($f['supervisor'])
            . '</td><td><span class="status ' . h($f['status']) . '">' . h(status_label($f['status'])) . '</span></td>'
            . ($token ? '<td>' . action_form((int) $f['formID'], $token) . '</td>' : '') . '</tr>';
    }
    echo '</table></div>';
}
