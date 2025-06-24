<?php
header("Access-Control-Allow-Origin: *");

// Open connection to MySQL db
require_once 'config.php';
// Fetch the last five orders from mysql db
$sql = "SELECT * FROM `tblorders` 
        LEFT JOIN tblorderStatus ON ordState = ordstatusId 
        LEFT JOIN tblusersAddresses ON uadId = ordaddId 
        WHERE ordState != 0  
        ORDER BY ordId DESC 
        LIMIT 5";  // Added LIMIT 5 to retrieve only the last five orders
$result = mysqli_query($con, $sql) or die("Error in Selecting " . mysqli_error($con));

// Create an array
$ordersArray = array();
while ($row = mysqli_fetch_assoc($result)) {
    $ordersArray[] = $row;
}
echo json_encode($ordersArray);
?>
