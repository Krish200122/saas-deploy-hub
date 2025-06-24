<?php
header("Access-Control-Allow-Origin: *");

// Open connection to MySQL db
require_once 'config.php';
// Fetch the total number of IDs from the tblusers table
$sql = "SELECT COUNT(userId) AS total_ids FROM `tblusers`";
$result = mysqli_query($con, $sql) or die("Error in Selecting " . mysqli_error($con));

// Fetch the result row as an associative array
$row = mysqli_fetch_assoc($result);

// Fetch the total number of orders from the tblorders table
$sql1 = "SELECT COUNT(ordId) AS total_ord FROM `tblorders`";
$result1 = mysqli_query($con, $sql1) or die("Error in Selecting " . mysqli_error($con));

// Fetch the result row as an associative array
$row1 = mysqli_fetch_assoc($result1);

// Fetch the total Amount of orders from the tblorders table
$sql2 = "SELECT SUM(ordTotal) AS total_amount FROM tblorders where ordState=4";
$result2 = mysqli_query($con, $sql2) or die("Error in Selecting " . mysqli_error($con));

$row2 = mysqli_fetch_assoc($result2);
// Close the database connection
mysqli_close($con);

// Combine the rows into an array
$data = array("tblusers" => $row, "tblorders" => $row1, "tblorders2" => $row2);

// Return the data as JSON
echo json_encode($data);
?>
