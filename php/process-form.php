<?php

$first_name = $_POST["first_name"];
$last_name = $_POST["last_name"];
$email = $_POST["email"];
$title = $_POST["title"];
$netid = $_POST["netid"];
$sup_name = $_POST["sup_name"];
$sup_email = $_POST["sup_email"];
$request = $_POST["request"]; //put validate function later
$employee = $_POST["employee"];

$host = "localhost";
$dbname = "rutgersaccessgrantsystem";
$username = "root";
$password = "root";

$conn = mysqli_connect($host,$username,$password,$dbname);

if(mysqli_connect_errno()){
    die("Connection Error: " . mysqli_connect_errno());
}

$sql = "INSERT INTO demoTable (`First Name`, `Last Name`, `Email`, `Title`, `NetId`, `Sup_Name`, `Sup_Email`, `Request`, `Employee`) 
        VALUES (?,?,?,?,?,?,?,?,?);";

$stmt = mysqli_stmt_init($conn);

if(!mysqli_stmt_prepare($stmt,$sql)){
    die(mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt,"sssssssss",
                    $first_name,
                    $last_name,
                    $email,
                    $title,
                    $netid,
                    $sup_name,
                    $sup_email,
                    $request,
                    $employee);

mysqli_stmt_execute($stmt);
echo "Record save:";

?>
