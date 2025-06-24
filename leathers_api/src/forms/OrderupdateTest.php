<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
$jsonValue = file_get_contents('php://input');
$jsonValue = html_entity_decode($jsonValue);
$Value = json_decode($jsonValue);
 
// Open a connection to the MySQL database
require_once 'config.php';
$msg = "";
 
// class Result
// {
// }
 
$orderId = $Value->{'ordid'};
$newOrdState = $Value->{'ordstatusid'}; // New state value
$newOrdTotal = $Value->{'ordtotal'}; // New total value
$newAddId = $Value->{'addid'}; // New addId value
$newOrdDis = $Value->{'discount'};
 
 
 
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
 
// Update tblorders table
$sqlOrders = "UPDATE `tblorders`
              SET `ordaddId` = '$newAddId',
                  `ordDiscount` = '$newOrdDis'
              WHERE `ordId` = '$orderId'";
 
 
// Update tblorderdetails table using a subquery
// $sqlOrderDetails = "UPDATE `tblorderDetails`
//                    SET `ordStatus` = '$newOrdState'
//                    WHERE `orddetOrder` = '$orderId' AND `ordStatus` IN (0, 7)";
 
 
if ($con->query($sqlOrders) === TRUE) {
    $msg = "Order details in tblorders updated successfully. ";
} else {
    $msg .= "Error updating tblorders: " . $con->error . ". ";
}
 
if ($con->query($sqlOrderDetails) === TRUE) {
    $msg .= "Order details in tblorderdetails updated successfully.";
} else {
    $msg .= "Error updating tblorderdetails: " . $con->error . ". ";
}
 
$result = new Result();
$result->message = $msg;
 
echo json_encode($result);
 
// Close the database connection
mysqli_close($con);
?>
 
 