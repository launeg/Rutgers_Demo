<?php
$host = "localhost";
$dbname = "rutgersaccessgrantsystem";
$username = "root";
$password = "root";

// Create connection
$conn = mysqli_connect($host,$username,$password,$dbname);
$host = "localhost:8888";
$dbname = "rutgersaccessgrantsystem";
$username = "root";
$password = "root";

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Close connection
$conn->close();

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($data);
?>