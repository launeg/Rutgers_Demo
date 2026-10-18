<?php
/**
 * All database access. Every query uses prepared statements.
 * Pure rules (validation, which queue a role sees) are separate functions
 * so they can be unit tested without a database.
 */
require_once __DIR__ . '/../config/config.php';

const FORM_STATUSES = ['pending_supervisor', 'pending_role_manager', 'pending_it', 'approved', 'denied'];

/** Run a prepared SELECT and return all rows. */
function query_all(mysqli $conn, string $sql, string $types = '', array $params = []): array
{
    $stmt = $conn->prepare($sql);
    if ($types !== '') {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function find_user(mysqli $conn, int $userId): ?array
{
    $rows = query_all($conn, 'SELECT userID, netID, firstName, lastName, userRole FROM users WHERE userID = ?', 'i', [$userId]);
    return $rows[0] ?? null;
}

function list_users(mysqli $conn): array
{
    return query_all($conn, 'SELECT userID, netID, firstName, lastName, userRole FROM users ORDER BY userRole, lastName');
}

/** Dropdown and checkbox choices for the request form. */
function form_options(mysqli $conn): array
{
    return [
        'departments'   => query_all($conn, 'SELECT schoolDeptID AS id, CONCAT(schoolName, " / ", deptName) AS label FROM schoolanddepartment ORDER BY label'),
        'requestTypes'  => query_all($conn, 'SELECT requestID AS id, label FROM requesttypes ORDER BY requestID'),
        'employeeTypes' => query_all($conn, 'SELECT empTypeID AS id, label FROM employeetypes ORDER BY empTypeID'),
        'supervisors'   => query_all($conn, 'SELECT userID AS id, CONCAT(firstName, " ", lastName) AS label FROM users WHERE userRole = "supervisor" ORDER BY lastName'),
        'systems'       => query_all($conn, 'SELECT systemID AS id, label FROM application ORDER BY systemID'),
        'roles'         => query_all($conn, 'SELECT r.roleID AS id, a.label AS systemLabel, r.label FROM roles r JOIN application a ON a.systemID = r.systemID ORDER BY a.systemID, r.roleID'),
    ];
}

/**
 * Validate submitted form input against the available options.
 *
 * @return string[] error messages keyed by field name, empty when valid
 */
function validate_form(array $in, array $options): array
{
    $errors = [];
    $required = [
        'firstName' => 'First name', 'lastName' => 'Last name', 'justification' => 'Justification',
        'approvalName' => 'Name', 'approvalSignature' => 'Signature',
    ];
    foreach ($required as $field => $label) {
        if (trim($in[$field] ?? '') === '') {
            $errors[$field] = "$label is required.";
        }
    }
    if (!preg_match('/^[a-z0-9]{2,50}$/i', $in['netid'] ?? '')) {
        $errors['netid'] = 'NetID must be 2-50 letters or digits.';
    }
    if (!filter_var($in['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email.';
    }
    foreach (['effDate' => 'Enter a valid effective date.', 'approvalDate' => 'Enter a valid date.'] as $field => $message) {
        $date = DateTime::createFromFormat('Y-m-d', $in[$field] ?? '');
        if (!$date || $date->format('Y-m-d') !== $in[$field]) {
            $errors[$field] = $message;
        }
    }

    $choices = ['departmentId' => 'departments', 'requestTypeId' => 'requestTypes', 'employeeTypeId' => 'employeeTypes', 'supervisorId' => 'supervisors'];
    foreach ($choices as $field => $key) {
        $valid = array_column($options[$key], 'id');
        if (!in_array((string) ($in[$field] ?? ''), array_map('strval', $valid), true)) {
            $errors[$field] = 'Choose a valid option.';
        }
    }

    $roleIds = $in['roleIds'] ?? [];
    $validRoles = array_map('strval', array_column($options['roles'], 'id'));
    if (!$roleIds || array_diff(array_map('strval', $roleIds), $validRoles)) {
        $errors['roleIds'] = 'Choose at least one role.';
    }
    return $errors;
}

/**
 * Save a new request, its requested roles and a "submit" log entry in one transaction.
 *
 * @return int the new formID
 */
function create_form(mysqli $conn, int $submitterId, array $in): int
{
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare(
            'INSERT INTO forms (submitterUserID, supervisorUserID, userFirstName, userLastName, userEmail, userNetID,
                                schoolDeptID, requestID, empTypeID, effDate, userJustification)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $supervisorId = (int) $in['supervisorId'];
        $departmentId = (int) $in['departmentId'];
        $requestTypeId = (int) $in['requestTypeId'];
        $employeeTypeId = (int) $in['employeeTypeId'];
        $stmt->bind_param(
            'iissssiiiss',
            $submitterId, $supervisorId, $in['firstName'], $in['lastName'], $in['email'], $in['netid'],
            $departmentId, $requestTypeId, $employeeTypeId, $in['effDate'], $in['justification']
        );
        $stmt->execute();
        $formId = $conn->insert_id;

        $roleStmt = $conn->prepare('INSERT INTO form_roles (formID, roleID) VALUES (?, ?)');
        foreach ($in['roleIds'] as $roleId) {
            $roleId = (int) $roleId;
            $roleStmt->bind_param('ii', $formId, $roleId);
            $roleStmt->execute();
        }

        $logStmt = $conn->prepare(
            'INSERT INTO formupdatelog (formID, userID, updateTypeID)
             SELECT ?, ?, updateTypeID FROM formupdatelogtypes WHERE actionName = "submit"'
        );
        $logStmt->bind_param('ii', $formId, $submitterId);
        $logStmt->execute();

        $conn->commit();
        return $formId;
    } catch (Throwable $e) {
        $conn->rollback();
        throw $e;
    }
}

const FORM_LIST_SQL = 'SELECT f.formID, f.entryDate, f.effDate, f.status, f.userNetID,
        CONCAT(f.userFirstName, " ", f.userLastName) AS employee,
        CONCAT(sd.schoolName, " / ", sd.deptName) AS department,
        CONCAT(s.firstName, " ", s.lastName) AS supervisor,
        rt.label AS requestType,
        GROUP_CONCAT(CONCAT(a.label, ": ", r.label) ORDER BY a.systemID, r.roleID SEPARATOR ", ") AS roles
    FROM forms f
    JOIN schoolanddepartment sd ON sd.schoolDeptID = f.schoolDeptID
    JOIN users s ON s.userID = f.supervisorUserID
    JOIN requesttypes rt ON rt.requestID = f.requestID
    LEFT JOIN form_roles fr ON fr.formID = f.formID
    LEFT JOIN roles r ON r.roleID = fr.roleID
    LEFT JOIN application a ON a.systemID = r.systemID';

/**
 * Extra WHERE conditions for the request list, built from search/filter input.
 * Empty or unknown values are ignored. System and role use EXISTS so the roles
 * column still lists every role on a matching request.
 *
 * @return array{0: string, 1: string, 2: array} SQL to append, bind types, bind values
 */
function form_filter_sql(array $filters): array
{
    $sql = '';
    $types = '';
    $params = [];

    $search = trim((string) ($filters['q'] ?? ''));
    if ($search !== '') {
        $like = '%' . addcslashes($search, '%_\\') . '%';
        $sql .= ' AND (CONCAT(f.userFirstName, " ", f.userLastName) LIKE ? OR f.userNetID LIKE ? OR f.userEmail LIKE ?)';
        $types .= 'sss';
        array_push($params, $like, $like, $like);
    }
    if (in_array($filters['status'] ?? '', FORM_STATUSES, true)) {
        $sql .= ' AND f.status = ?';
        $types .= 's';
        $params[] = $filters['status'];
    }

    $idFilters = [
        'department' => ' AND f.schoolDeptID = ?',
        'system'     => ' AND EXISTS (SELECT 1 FROM form_roles xfr JOIN roles xr ON xr.roleID = xfr.roleID WHERE xfr.formID = f.formID AND xr.systemID = ?)',
        'role'       => ' AND EXISTS (SELECT 1 FROM form_roles xfr WHERE xfr.formID = f.formID AND xfr.roleID = ?)',
    ];
    foreach ($idFilters as $key => $condition) {
        $id = filter_var($filters[$key] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id !== false) {
            $sql .= $condition;
            $types .= 'i';
            $params[] = $id;
        }
    }
    return [$sql, $types, $params];
}

function forms_by_submitter(mysqli $conn, int $userId, array $filters = []): array
{
    [$where, $types, $params] = form_filter_sql($filters);
    return query_all(
        $conn,
        FORM_LIST_SQL . ' WHERE f.submitterUserID = ?' . $where . ' GROUP BY f.formID ORDER BY f.entryDate DESC',
        'i' . $types,
        [$userId, ...$params]
    );
}

/** Status a user with this role acts on, or null if the role has no approval queue. */
function queue_status_for_role(string $role): ?string
{
    return [
        'supervisor'   => 'pending_supervisor',
        'role_manager' => 'pending_role_manager',
        'it_admin'     => 'pending_it',
    ][$role] ?? null;
}

/** Requests waiting for this user. Supervisors only see requests that name them. */
function approval_queue(mysqli $conn, array $user): array
{
    $status = queue_status_for_role($user['userRole']);
    if ($status === null) {
        return [];
    }
    if ($user['userRole'] === 'supervisor') {
        return query_all($conn, FORM_LIST_SQL . ' WHERE f.status = ? AND f.supervisorUserID = ? GROUP BY f.formID ORDER BY f.entryDate', 'si', [$status, $user['userID']]);
    }
    return query_all($conn, FORM_LIST_SQL . ' WHERE f.status = ? GROUP BY f.formID ORDER BY f.entryDate', 's', [$status]);
}
