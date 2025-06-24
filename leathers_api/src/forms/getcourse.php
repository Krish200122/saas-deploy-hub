<?php
header("Access-Control-Allow-Origin: *");

// Database connection parameters
$host = "localhost";
$username = "sqladmin1";
$password = "P@ssw0rd12345";
$database = "College_Management";

// Create connection
require_once 'config.php';

$sql = "SELECT * FROM `tblCourse`";
$result = mysqli_query($con, $sql) or die("Error in select course: " . mysqli_error($con));

$coursearray = array();

while ($row = mysqli_fetch_assoc($result)) {
    // Replace null values with empty strings
    array_walk_recursive($row, function(&$value) {
        $value = $value === null ? '' : $value;
    });
    $coursearray[] = $row;
}

// Return JSON response
echo json_encode($coursearray);

// Close the database connection
mysqli_close($con);
?>