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

/** Table of requests, shared by the submitter and approver pages. */
function render_form_table(array $forms): void
{
    if (!$forms) {
        echo '<p class="summary">No requests.</p>';
        return;
    }
    echo '<div class="table-information"><table class="table2"><tr><th>Date Sub</th><th>Date Eff</th><th>Employee</th><th>NetID</th>'
        . '<th>Department</th><th>Request</th><th>Roles</th><th>Supervisor</th><th>Status</th></tr>';
    foreach ($forms as $f) {
        echo '<tr><td>' . h(substr($f['entryDate'], 0, 10)) . '</td><td>' . h($f['effDate']) . '</td><td>' . h($f['employee'])
            . '</td><td>' . h($f['userNetID']) . '</td><td>' . h($f['department']) . '</td><td>' . h($f['requestName'])
            . '</td><td>' . h($f['roles']) . '</td><td>' . h($f['supervisor'])
            . '</td><td><span class="status ' . h($f['status']) . '">' . h(status_label($f['status'])) . '</span></td></tr>';
    }
    echo '</table></div>';
}
