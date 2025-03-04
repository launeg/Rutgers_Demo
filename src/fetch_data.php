<?php
include 'connect.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
// SQL query to fetch data
$sql = "SELECT 
            entryDate, 
            effDate, 
            userNetID, 
            userFirstName, 
            userLastName, 
            systemID, 
            roleID, 
            schoolDeptID, 
            supervisorName, 
            lastAction, 
            lastActionDate 
        FROM forms";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data as JSON
    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // Close connection
    $conn->close();

    // Return data as JSON
    header('Content-Type: application/json');
    echo json_encode($data);
} else {
    echo "0 results";
}

// Close connection
$conn->close();
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
