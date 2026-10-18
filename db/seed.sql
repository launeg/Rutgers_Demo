-- Fictional demo data for local development and e2e tests. Never use in production.

INSERT INTO users (userID, netID, firstName, lastName, email, userRole) VALUES
  (1, 'ap1001', 'Alex',   'Submitter',   'alex.submitter@example.com',  'submitter'),
  (2, 'bp1002', 'Blake',  'Submitter',   'blake.submitter@example.com', 'submitter'),
  (3, 'sp2001', 'Sam',    'Supervisor',  'sam.supervisor@example.com',  'supervisor'),
  (4, 'rm3001', 'Riley',  'RoleManager', 'riley.roles@example.com',     'role_manager'),
  (5, 'it4001', 'Indy',   'ITAdmin',     'indy.it@example.com',         'it_admin');

INSERT INTO forms (formID, submitterUserID, supervisorUserID, userFirstName, userLastName, userEmail,
                   userNetID, schoolDeptID, requestID, empTypeID, effDate, userJustification, status, entryDate) VALUES
  (1, 1, 3, 'Casey',  'Newhire', 'casey.newhire@example.com',  'cn5001', 1, 1, 1, '2030-01-15', 'New advisor needs GradTracker access.',       'pending_supervisor',   '2030-01-02 09:00:00'),
  (2, 1, 3, 'Devon',  'Faculty', 'devon.faculty@example.com',  'df5002', 2, 2, 2, '2030-01-20', 'Faculty member needs AMS and START.',         'pending_role_manager', '2030-01-03 10:30:00'),
  (3, 2, 3, 'Emery',  'Staff',   'emery.staff@example.com',    'es5003', 3, 3, 1, '2030-02-01', 'Staff member needs to manage the SASN Website.', 'pending_it',         '2030-01-04 14:15:00'),
  (4, 2, 3, 'Finley', 'Student', 'finley.student@example.com', 'fs5004', 4, 1, 3, '2030-02-10', 'Student worker needs Kronos access.',        'approved',             '2030-01-05 08:45:00');

INSERT INTO form_roles (formID, roleID) VALUES
  (1, (SELECT roleID FROM roles WHERE roleName = 'gt_certifier')),
  (2, (SELECT roleID FROM roles WHERE roleName = 'ams_certifier')),
  (2, (SELECT roleID FROM roles WHERE roleName = 'admin')),
  (3, (SELECT roleID FROM roles WHERE roleName = 'sasn_supervisor')),
  (4, (SELECT roleID FROM roles WHERE roleName = 'kr_informational'));

INSERT INTO formupdatelog (formID, userID, updateTypeID, comment, createdAt) VALUES
  (1, 1, (SELECT updateTypeID FROM formupdatelogtypes WHERE actionName = 'submit'),  NULL,               '2030-01-02 09:00:00'),
  (2, 1, (SELECT updateTypeID FROM formupdatelogtypes WHERE actionName = 'submit'),  NULL,               '2030-01-03 10:30:00'),
  (2, 3, (SELECT updateTypeID FROM formupdatelogtypes WHERE actionName = 'approve'), 'Looks good.',      '2030-01-03 15:00:00'),
  (3, 2, (SELECT updateTypeID FROM formupdatelogtypes WHERE actionName = 'submit'),  NULL,               '2030-01-04 14:15:00'),
  (3, 3, (SELECT updateTypeID FROM formupdatelogtypes WHERE actionName = 'approve'), NULL,               '2030-01-04 16:00:00'),
  (3, 4, (SELECT updateTypeID FROM formupdatelogtypes WHERE actionName = 'approve'), 'Role is correct.', '2030-01-05 09:30:00'),
  (4, 2, (SELECT updateTypeID FROM formupdatelogtypes WHERE actionName = 'submit'),  NULL,               '2030-01-05 08:45:00'),
  (4, 3, (SELECT updateTypeID FROM formupdatelogtypes WHERE actionName = 'approve'), NULL,               '2030-01-05 11:00:00'),
  (4, 4, (SELECT updateTypeID FROM formupdatelogtypes WHERE actionName = 'approve'), NULL,               '2030-01-05 13:00:00'),
  (4, 5, (SELECT updateTypeID FROM formupdatelogtypes WHERE actionName = 'approve'), 'Access granted.',  '2030-01-06 09:00:00');
