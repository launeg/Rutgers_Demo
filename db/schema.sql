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
  systemName   VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE roles (
  roleID       INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  systemID     INT UNSIGNED NOT NULL,
  roleName     VARCHAR(100) NOT NULL,
  UNIQUE KEY uq_system_role (systemID, roleName),
  CONSTRAINT fk_roles_application FOREIGN KEY (systemID) REFERENCES application (systemID)
) ENGINE=InnoDB;

CREATE TABLE requesttypes (
  requestID    INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  requestName  VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE employeetypes (
  empTypeID    INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  empType      VARCHAR(100) NOT NULL UNIQUE
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
INSERT INTO application (systemName) VALUES
  ('START'), ('GradTracker'), ('JTracker'), ('AMS'),
  ('FacultyIntegration'), ('SASNWebsite'), ('Kronos');

INSERT INTO roles (systemID, roleName)
SELECT a.systemID, r.roleName FROM (
  SELECT 'START' AS sys, 'admin' AS roleName
  UNION ALL SELECT 'START', 'viewer'
  UNION ALL SELECT 'GradTracker', 'gt_certifier'
  UNION ALL SELECT 'GradTracker', 'gt_viewer'
  UNION ALL SELECT 'JTracker', 'jt_admin'
  UNION ALL SELECT 'JTracker', 'jt_viewer'
  UNION ALL SELECT 'AMS', 'ams_admin'
  UNION ALL SELECT 'AMS', 'ams_viewer'
  UNION ALL SELECT 'FacultyIntegration', 'fi_editor'
  UNION ALL SELECT 'SASNWebsite', 'web_editor'
  UNION ALL SELECT 'Kronos', 'timekeeper'
) r JOIN application a ON a.systemName = r.sys;

INSERT INTO requesttypes (requestName) VALUES ('newEmp'), ('changeRole'), ('removeAccess');
INSERT INTO employeetypes (empType) VALUES ('staff'), ('faculty'), ('student worker');
INSERT INTO formupdatelogtypes (actionName) VALUES ('submit'), ('approve'), ('deny');
INSERT INTO schoolanddepartment (schoolName, deptName) VALUES
  ('SASN', 'AAAS'), ('SASN', 'Biology'), ('SASN', 'History'), ('SASN', 'Computer Science');
