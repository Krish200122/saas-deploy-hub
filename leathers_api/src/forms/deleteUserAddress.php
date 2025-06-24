<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


$jsonValue = file_get_contents('php://input');
$jsonValue = html_entity_decode($jsonValue);
$Value = json_decode($jsonValue, true); 

 
$userId = $Value['uadId'] ?? null;


require_once 'config.php';

 
   
     
   $sql1 = "DELETE FROM tblusersAddresses WHERE uadId = '$userId'";
   if (mysqli_query($con, $sql1)) {
            $response = array("message" => "address deleted successfully", "status" => "success");
        } else {
            $response = array("message" => "error in deleting", "status" => "error");
        }


// Close the database connection
mysqli_close($con);

echo json_encode($response);

?>