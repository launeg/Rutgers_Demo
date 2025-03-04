<?php
//Variables for forms table
$firstName = $_POST["firstName"];
$lastName = $_POST["lastName"];
$email = $_POST["email"];
$entryDate = date("Y-m-d");
$effdate = $_POST["effdate"];
$netid = $_POST["netid"];
$userJustification = $_POST["justStart"]; // new
$actionString = $_POST["action"];
$action = ($actionString === "add") ? true : (($actionString === "delete") ? false : false);
echo '<pre>';
var_dump(
    $firstName,
    $lastName,
    $email,
    $entryDate,
    $effdate,
    $netid,
    $userJustification,
    $action
    );
echo '</pre>';

// Variables for supervisorInfo table (Lucas added)
$supervisorEmail = "";
$supervisorName = "";
$supervisorEmail = $_POST["supEmail"]; 
$supervisorName = $_POST["supName"]; 
echo '<pre>';
var_dump($supervisorEmail, $supervisorName);
echo '</pre>';

//Variable for schoolandDepartment
$School = $_POST["School"];
$Department = $_POST["Department"];

echo '<pre>';
var_dump(
    $School,
    $Department
);
echo '</pre>';

//Variable for users table
echo '<pre>';
var_dump(
    $netid,
    $firstName,
    $lastName,
    $email
);
echo '</pre>';

//variables for requestType table
$request = $_POST["request"];
echo '<pre>';
var_dump($request);
echo '</pre>';

//variables for employeeType
$employee = $_POST["employee"];
echo '<pre>';
var_dump($employee);
echo '</pre>';

//variables for application
$START = $_POST["START"];
$GradTracker = $_POST["GradTracker"];
$jTracker = $_POST["jTracker"];
$AMS = $_POST["AMS"];
$facultyInt = $_POST["facultyInt"];
$sasnWeb = $_POST["sasnWeb"];
$kronos = $_POST["kronos"];

$systemName = "";

if(!empty($START)){
    $systemName = $systemName."START ";
}
if (!empty($GradTracker)) {
    $systemName = $systemName."GradTracker ";
}
if (!empty($jTracker)) {
    $systemName = $systemName."jTracker ";
}
if (!empty($AMS)) {
    $systemName = $systemName."AMS ";
}
if (!empty($facultyInt)) {
    $systemName = $systemName."FacultyIntegration ";
}
if (!empty($sasnWeb)) {
    $systemName = $systemName."SASNWebsite ";
}
if (!empty($kronos)) {
    $systemName = $systemName."Kronos ";
}



echo '<pre>';
var_dump($START,$GradTracker,$jTracker,$AMS,$facultyInt,$sasnWeb,$kronos);
echo '</pre>';

$roles = $START;




//Connection info
$host = "localhost";
$dbname = "rutgersaccessgrantsystem";
$user = "root";
$password = "root";
$conn = mysqli_connect($host,$user,$password, $dbname);

if(mysqli_connect_errno()){
    echo "Connection Error \n". mysqli_connect_error();
}else{
    echo "Connection successful \n";
}


// sql query to insert supervisors' email and name (Lucas added)
$sql_supervisorInfo = "INSERT INTO `supervisorInfo`(`supervisorName`, `supervisorEmail`) 
                       VALUES ('$supervisorName', '$supervisorEmail')";
$stmt_supervisorInfo = mysqli_stmt_init($conn);
if($sql_supervisorInfo){
    echo "Yup";
} else {
    echo "Nope.";
}

//sql qeury for school table
$sql_schoolandDepartment = "INSERT INTO `schoolanddepartment`(`schoolName`, `debtName`) 
                            VALUES (?, ?)";
$stmt_schoolandDepartment = mysqli_stmt_init($conn);

//sql qeury for users table
$sql_users = "INSERT INTO `users`(`netID`, `firstName`, `lastName`, `email`) 
              VALUES (?,?,?,?)";
$stmt_users = mysqli_stmt_init($conn);


//sql query for reqeusttype table
$sql_requestType = "INSERT INTO `requesttypes`(`requestName`) 
                    VALUES (?)";
$stmt_requestType = mysqli_stmt_init($conn);

// query for employeetype
$sql_employeeType = "INSERT INTO `employeetypes`(`empType`) VALUES (?)";
$stmt_employeeType = mysqli_stmt_init($conn);

$sql_application = "INSERT INTO `application`(`systemName`) VALUES (?)";
$stmt_application = mysqli_stmt_init($conn);

//roles
$rolesNames = $roles;

$systemIDResult = mysqli_query($conn, "SELECT application.systemID FROM application ORDER BY systemID DESC LIMIT 1;");
$systemIDRow = mysqli_fetch_assoc($systemIDResult);
$systemID = $systemIDRow['systemID'];

$roles_systemID = $systemID; 

$sql_roles = "INSERT INTO `roles` (systemID, rolesNames)
              VALUES (?,?)";

$stmt_roles = mysqli_stmt_init($conn);

//fomrupdatelogtypes
$actionName = "submit";
$sql_UpdateLog = "INSERT INTO `formupdatelogtypes`(`actionName`) VALUES (?)";
$stmt_UpdateLog = mysqli_stmt_init($conn);



$userIDResult = mysqli_query($conn, "SELECT users.userID FROM users ORDER BY userID DESC LIMIT 1;");
$userIDRow = mysqli_fetch_assoc($userIDResult);
$userID = $userIDRow['userID'];

$formIDResult = mysqli_query($conn, "SELECT forms.formID FROM forms ORDER BY formID DESC LIMIT 1;");
$formIDRow = mysqli_fetch_assoc($roleIDResult);
$formID = $formIDRow['formID'];

// (Lucas code)
if(!mysqli_stmt_prepare($stmt_supervisorInfo, $sql_supervisorInfo)){
    die(mysqli_error($conn));

}

if(!mysqli_stmt_prepare($stmt_schoolandDepartment, $sql_schoolandDepartment)){
    die(mysqli_error($conn));
}

if (!mysqli_stmt_prepare($stmt_users, $sql_users)) {
    die(mysqli_error($conn));
}

if (!mysqli_stmt_prepare($stmt_requestType, $sql_requestType)) {
    die(mysqli_error($conn));
} 

if (!mysqli_stmt_prepare($stmt_employeeType, $sql_employeeType)) {
    die(mysqli_error($conn));
} 

if (!mysqli_stmt_prepare($stmt_application, $sql_application)) {
    die(mysqli_error($conn));
} 

if (!mysqli_stmt_prepare($stmt_roles, $sql_roles)) {
    die(mysqli_error($conn));
}

if (!mysqli_stmt_prepare($stmt_UpdateLog, $sql_UpdateLog)) {
    die(mysqli_error($conn));
}

// bind parem for supervisor information (Lucas added)
mysqli_stmt_bind_param($stmt_supervisorInfo, "ss", $supervisorName, $supervisorEmail);
mysqli_stmt_execute($stmt_supervisorInfo);
echo "Supervisor Record Saved \n";


//bind parem for school and department table
mysqli_stmt_bind_param($stmt_schoolandDepartment, "ss", $School, $Department);
mysqli_stmt_execute($stmt_schoolandDepartment);
echo "School Record Saved \n";


//bind parem for user table
mysqli_stmt_bind_param($stmt_users, "ssss", $netid, $firstName, $lastName, $email);
mysqli_stmt_execute($stmt_users);
echo "User Record Saved \n";

mysqli_stmt_bind_param($stmt_requestType, "s", $request);
mysqli_stmt_execute($stmt_requestType);
echo "RequestType Record saved \n";

mysqli_stmt_bind_param($stmt_employeeType, "s", $employee);
mysqli_stmt_execute($stmt_employeeType);
echo "EmployeeType Record Saved \n";

mysqli_stmt_bind_param($stmt_application,"s",$systemName);
mysqli_stmt_execute($stmt_application);
echo "Application Record Saved \n";

mysqli_stmt_bind_param($stmt_roles, "is",$roles_systemID, $rolesNames);
mysqli_stmt_execute($stmt_roles);
echo "Roles Record Saved \n";


mysqli_stmt_bind_param($stmt_UpdateLog, "s", $actionName);
mysqli_stmt_execute($stmt_UpdateLog);
echo "Update Log Record Saved \n";



//form update log type
$updateTypeIDResult = mysqli_query($conn, "SELECT formupdatelogtypes.updateTypeID FROM formupdatelogtypes ORDER BY updateTypeID DESC LIMIT 1;");
$updateTypeIDRow = mysqli_fetch_assoc($roleIDResult);
$updateTypeID = $updateTypeIDRow['updateTypeID'];

$formUpdateLog_userID = $userID;
$formUpdateLog_formID = $formID;
$formUpdateLog_UpdateTypeID = $updateTypeID;

$sql_formUpdateLog = "INSERT INTO `formupdatelog`( `userID`, `formID`, `updateTypeID`) 
                      VALUES (?,?,?)";
$stmt_formUpdateLog = mysqli_stmt_init($conn);

if (!mysqli_stmt_prepare($stmt_formUpdateLog, $sql_formUpdateLog)) {
    die(mysqli_error($conn));

}

mysqli_stmt_bind_param($stmt_formUpdateLog,"iii",$formUpdateLog_userID,$formUpdateLog_formID,$formUpdateLog_UpdateTypeID);
mysqli_stmt_execute($stmt_formUpdateLog);
echo "Form Update Log Record Saved\n";





//access table
$userIDResult = mysqli_query($conn, "SELECT users.userID FROM users ORDER BY userID DESC LIMIT 1;");
$userIDRow = mysqli_fetch_assoc($userIDResult);
$userID = $userIDRow['userID'];

$roleIDResult = mysqli_query($conn, "SELECT roles.roleID FROM roles ORDER BY roleID DESC LIMIT 1;");
$roleIDRow = mysqli_fetch_assoc($roleIDResult);
$roleID = $roleIDRow['roleID'];

$access_UserID = $userID; // Use the previously fetched $userID
$access_roleID = $roleID; // Use the previously fetched $roleID

$sql_access = "INSERT INTO `access`(`userID`, `roleID`) 
               VALUES (?,?)";
$stmt_access = mysqli_stmt_init($conn);

if (!mysqli_stmt_prepare($stmt_access, $sql_access)) {
    die(mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt_access, "ii", $access_UserID, $access_roleID);
mysqli_stmt_execute($stmt_access);
echo "Access Record Saved \n";



// (Lucas added)
$supervisorResult = mysqli_query($conn, "SELECT supervisorInfo.supervisorID FROM supervisorInfo ORDER BY supervisorID DESC LIMIT 1;");
$supervisorRow = mysqli_fetch_row($supervisorResult);
$supervisorID = $supervisorRow['$supervisorID'];

//sql qeury for forms table
$systemIDResult = mysqli_query($conn, "SELECT application.systemID FROM application ORDER BY systemID DESC LIMIT 1;");
$systemIDRow = mysqli_fetch_assoc($systemIDResult);
$systemID = $systemIDRow['systemID'];

$roleIDResult = mysqli_query($conn, "SELECT roles.roleID FROM roles ORDER BY roleID DESC LIMIT 1;");
$roleIDRow = mysqli_fetch_assoc($roleIDResult);
$roleID = $roleIDRow['roleID'];

$schoolDeptIDResult = mysqli_query($conn, "SELECT schoolanddepartment.schoolDeptID FROM schoolanddepartment ORDER BY schooldeptID DESC LIMIT 1;");
$schoolDeptIDRow = mysqli_fetch_assoc($schoolDeptIDResult);
$schoolDeptID = $schoolDeptIDRow['schoolDeptID'];

$userIDResult = mysqli_query($conn, "SELECT users.userID FROM users ORDER BY userID DESC LIMIT 1;");
$userIDRow = mysqli_fetch_assoc($userIDResult);
$userID = $userIDRow['userID'];

$requestIDResult = mysqli_query($conn, "SELECT requesttypes.requestID FROM requesttypes ORDER BY requestID DESC LIMIT 1;");
$requestIDRow = mysqli_fetch_assoc($requestIDResult);
$requestID = $requestIDRow['requestID'];

$empTypeIDResult = mysqli_query($conn, "SELECT employeetypes.empTypeID FROM employeetypes ORDER BY empTypeID DESC LIMIT 1;");
$empTypeIDRow = mysqli_fetch_assoc($empTypeIDResult);
$empTypeID = $empTypeIDRow['empTypeID'];

$sql = "INSERT INTO `forms` (
    `userFirstName`, 
    `userLastName`, 
    `userEmail`, 
    `entryDate`, 
    `effDate`, 
    `userNetID`, 
    `systemID`, 
    `roleID`, 
    `schoolDeptID`, 
    `userJustification`, 
    `submitterUserID`, 
    `requestID`, 
    `userEmployeeID`, 
    `action`
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_stmt_init($conn);




if(!mysqli_stmt_prepare($stmt, $sql)){
    die(mysqli_error($conn));
}


//bind parem for form table
mysqli_stmt_bind_param(
    $stmt,
    "ssssssiiisiiii",
    $firstName,
    $lastName,
    $email,
    $entryDate,
    $effdate,
    $netid,
    $systemID,      // Use the fetched value
    $roleID,        // Use the fetched value
    $schoolDeptID,  // Use the fetched value
    $userJustification,
    $userID,
    $requestID,
    $empTypeID,
    $action
);
mysqli_stmt_execute($stmt);
echo "Record Saved \n";









//ignore this
/*
$title = $_POST["title"];
$sup_name = $_POST["sup_name"];3
$sup_email = $_POST["sup_email"];


$access_grantor_name = $_POST["access_grantor_name"];
$access_comment = $_POST["access_comment"];
$approvalName = $_POST["approvalName"];
$approvalSignature = $_POST["approvalSignature"];
$approvalDate = $_POST["approvalDate"];
$deanName = $_POST["deanName"];
$deanSignature = $_POST["deanSignature"];
$deanDate = $_POST["deanDate"];
$itName = $_POST["itName"];
$itSignature = $_POST["itSignature"];
$itDate = $_POST["itDate"];
*/

  /* $title,
    $sup_name,
    $sup_email,
    $access_grantor_name,
    $access_comment,
    $approvalName,
    $approvalSignature,
    $approvalDate,
    $deanName,
    $deanSignature,
    $deanDate,
    $itName,
    $itSignature,
    $itDate
);

*/

//form table done but still needs primary keys of other tables
// school and department table...DONE
//users table done...DONE
//requestType table done
//employeetype table done
//application table done