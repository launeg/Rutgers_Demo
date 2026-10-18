-- Schema plus lookup data. Loads on first container start.

CREATE TABLE users (
  userID       INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  netID        VARCHAR(50)  NOT NULL UNIQUE,
  firstName    VARCHAR(100) NOT NULL,
  lastName     VARCHAR(100) NOT NULL,
  email        VARCHAR(255) NOT NULL UNIQUE,
  userRole     ENUM('submitter','supervisor','role_manager','it_admin') NOT NULL DEFAULT 'submitter'
) ENGINE=InnoDB;

CREATE TABLE schoolanddepartment (
  schoolDeptID INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  schoolName   VARCHAR(100) NOT NULL,
  deptName     VARCHAR(100) NOT NULL,
  UNIQUE KEY uq_school_dept (schoolName, deptName)
) ENGINE=InnoDB;

CREATE TABLE application (
  systemID     INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  systemName   VARCHAR(100) NOT NULL UNIQUE,
  label        VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE roles (
  roleID       INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  systemID     INT UNSIGNED NOT NULL,
  roleName     VARCHAR(100) NOT NULL,
  label        VARCHAR(100) NOT NULL,
  UNIQUE KEY uq_system_role (systemID, roleName),
  UNIQUE KEY uq_system_role_label (systemID, label),
  CONSTRAINT fk_roles_application FOREIGN KEY (systemID) REFERENCES application (systemID)
) ENGINE=InnoDB;

CREATE TABLE requesttypes (
  requestID    INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  requestName  VARCHAR(100) NOT NULL UNIQUE,
  label        VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE employeetypes (
  empTypeID    INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  empType      VARCHAR(100) NOT NULL UNIQUE,
  label        VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE formupdatelogtypes (
  updateTypeID INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  actionName   VARCHAR(50)  NOT NULL UNIQUE
) ENGINE=InnoDB;

-- One access request. status is the current step of the approval workflow:
-- pending_supervisor -> pending_role_manager -> pending_it -> approved
-- (any step may move to denied).
CREATE TABLE forms (
  formID            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  submitterUserID   INT UNSIGNED NOT NULL,
  supervisorUserID  INT UNSIGNED NOT NULL,
  userFirstName     VARCHAR(100) NOT NULL,
  userLastName      VARCHAR(100) NOT NULL,
  userEmail         VARCHAR(255) NOT NULL,
  userNetID         VARCHAR(50)  NOT NULL,
  schoolDeptID      INT UNSIGNED NOT NULL,
  requestID         INT UNSIGNED NOT NULL,
  empTypeID         INT UNSIGNED NOT NULL,
  effDate           DATE         NOT NULL,
  userJustification TEXT         NOT NULL,
  status            ENUM('pending_supervisor','pending_role_manager','pending_it','approved','denied')
                    NOT NULL DEFAULT 'pending_supervisor',
  entryDate         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_forms_status (status),
  KEY idx_forms_submitter (submitterUserID),
  KEY idx_forms_supervisor (supervisorUserID),
  CONSTRAINT fk_forms_submitter  FOREIGN KEY (submitterUserID)  REFERENCES users (userID),
  CONSTRAINT fk_forms_supervisor FOREIGN KEY (supervisorUserID) REFERENCES users (userID),
  CONSTRAINT fk_forms_schooldept FOREIGN KEY (schoolDeptID)     REFERENCES schoolanddepartment (schoolDeptID),
  CONSTRAINT fk_forms_request    FOREIGN KEY (requestID)        REFERENCES requesttypes (requestID),
  CONSTRAINT fk_forms_emptype    FOREIGN KEY (empTypeID)        REFERENCES employeetypes (empTypeID)
) ENGINE=InnoDB;

-- Roles requested on a form (a form may request several systems/roles).
CREATE TABLE form_roles (
  formID INT UNSIGNED NOT NULL,
  roleID INT UNSIGNED NOT NULL,
  PRIMARY KEY (formID, roleID),
  CONSTRAINT fk_formroles_form FOREIGN KEY (formID) REFERENCES forms (formID) ON DELETE CASCADE,
  CONSTRAINT fk_formroles_role FOREIGN KEY (roleID) REFERENCES roles (roleID)
) ENGINE=InnoDB;

-- Append-only audit trail of workflow actions.
CREATE TABLE formupdatelog (
  updateID     INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  formID       INT UNSIGNED NOT NULL,
  userID       INT UNSIGNED NOT NULL,
  updateTypeID INT UNSIGNED NOT NULL,
  comment      VARCHAR(500) NULL,
  createdAt    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_log_form (formID),
  CONSTRAINT fk_log_form FOREIGN KEY (formID)       REFERENCES forms (formID) ON DELETE CASCADE,
  CONSTRAINT fk_log_user FOREIGN KEY (userID)       REFERENCES users (userID),
  CONSTRAINT fk_log_type FOREIGN KEY (updateTypeID) REFERENCES formupdatelogtypes (updateTypeID)
) ENGINE=InnoDB;

-- Lookup data the application needs in every environment.
-- The name columns are stable codes used by the code and seed data; label is what users see.
-- Codes and labels follow the original SASN IT Access Request Form.
INSERT INTO application (systemName, label) VALUES
  ('START', 'START(EMS)'), ('GradTracker', 'GradTracker'), ('JTracker', 'Junior Tracker'), ('AMS', 'AMS(PTL)'),
  ('FacultyIntegration', 'Faculty Integration'), ('SASNWebsite', 'SASN Website'), ('Kronos', 'Kronos');

INSERT INTO roles (systemID, roleName, label)
SELECT a.systemID, r.roleName, r.label FROM (
  SELECT 1 AS pos, 'admin' AS roleName, 'Admin' AS label
  UNION ALL SELECT 2, 'supervisor',    'Supervisor'
  UNION ALL SELECT 3, 'advisor',       'Student Advisor'
  UNION ALL SELECT 4, 'informational', 'Informational'
) r JOIN application a ON a.systemName = 'START'
ORDER BY r.pos;

-- Every other system offers the same eight roles, with a per-system code prefix.
INSERT INTO roles (systemID, roleName, label)
SELECT a.systemID, CONCAT(p.prefix, '_', r.code), r.label FROM (
  SELECT 'GradTracker' AS sys, 'gt' AS prefix
  UNION ALL SELECT 'JTracker',           'jt'
  UNION ALL SELECT 'AMS',                'ams'
  UNION ALL SELECT 'FacultyIntegration', 'fi'
  UNION ALL SELECT 'SASNWebsite',        'sasn'
  UNION ALL SELECT 'Kronos',             'kr'
) p
JOIN application a ON a.systemName = p.sys
CROSS JOIN (
  SELECT 1 AS pos, 'supervisor' AS code, 'Supervisor' AS label
  UNION ALL SELECT 2, 'certifier',     'Certifier'
  UNION ALL SELECT 3, 'deptCertifier', 'Department Certifier'
  UNION ALL SELECT 4, 'registrar',     'Registrar'
  UNION ALL SELECT 5, 'informational', 'Informational'
  UNION ALL SELECT 6, 'UTEP',          'Urban Teaching and Educational (UTEP)'
  UNION ALL SELECT 7, 'BAMA',          'BA/MA'
  UNION ALL SELECT 8, 'honors',        'Honors College'
) r
ORDER BY a.systemID, r.pos;

INSERT INTO requesttypes (requestName, label) VALUES
  ('newEmp', 'New Employee'), ('samePosNewRole', 'Same Position-New Role'),
  ('transfer', 'Transferring'), ('removeAllAccess', 'Remove All Access');
INSERT INTO employeetypes (empType, label) VALUES
  ('staff', 'Staff'), ('faculty', 'Faculty'), ('student_worker', 'Student Worker');
INSERT INTO formupdatelogtypes (actionName) VALUES ('submit'), ('approve'), ('deny');
INSERT INTO schoolanddepartment (schoolName, deptName) VALUES
  ('SASN', 'AAAS'), ('SASN', 'Biology'), ('SASN', 'History'), ('SASN', 'Computer Science');
