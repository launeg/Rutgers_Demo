<?php
/**
 * Approval workflow. next_status() and can_act() are pure so they can be unit tested.
 * Path: supervisor -> role manager -> IT -> approved. Any approver can deny.
 */
require_once __DIR__ . '/data.php';

const NEXT_STATUS = [
    'pending_supervisor'   => 'pending_role_manager',
    'pending_role_manager' => 'pending_it',
    'pending_it'           => 'approved',
];

/** Status after "approve" or "deny", or null if the request is finished or the action is unknown. */
function next_status(string $current, string $action): ?string
{
    if (!isset(NEXT_STATUS[$current])) {
        return null;
    }
    return ['approve' => NEXT_STATUS[$current], 'deny' => 'denied'][$action] ?? null;
}

/** A user acts only on their own stage. A supervisor must also be the one named on the request. */
function can_act(array $user, array $form): bool
{
    if (queue_status_for_role($user['userRole']) !== $form['status']) {
        return false;
    }
    return $user['userRole'] !== 'supervisor' || (int) $form['supervisorUserID'] === (int) $user['userID'];
}

/**
 * Approve or deny a request and write a log row, in one transaction.
 * The row is locked so two people cannot act on the same request at once.
 *
 * @return string|null an error message, or null on success
 */
function act_on_form(mysqli $conn, array $user, int $formId, string $action, string $comment): ?string
{
    $comment = substr(trim($comment), 0, 500);
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare('SELECT status, supervisorUserID FROM forms WHERE formID = ? FOR UPDATE');
        $stmt->bind_param('i', $formId);
        $stmt->execute();
        $form = $stmt->get_result()->fetch_assoc();

        if (!$form || !can_act($user, $form)) {
            $conn->rollback();
            return 'This request is not waiting for you.';
        }
        $new = next_status($form['status'], $action);
        if ($new === null) {
            $conn->rollback();
            return 'Unknown action.';
        }

        $update = $conn->prepare('UPDATE forms SET status = ? WHERE formID = ?');
        $update->bind_param('si', $new, $formId);
        $update->execute();

        $log = $conn->prepare(
            'INSERT INTO formupdatelog (formID, userID, updateTypeID, comment)
             SELECT ?, ?, updateTypeID, NULLIF(?, "") FROM formupdatelogtypes WHERE actionName = ?'
        );
        $log->bind_param('iiss', $formId, $user['userID'], $comment, $action);
        $log->execute();

        $conn->commit();
        return null;
    } catch (Throwable $e) {
        $conn->rollback();
        throw $e;
    }
}
