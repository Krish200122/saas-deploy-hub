<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


$jsonValue = file_get_contents('php://input');
$jsonValue = html_entity_decode($jsonValue);
$Value = json_decode($jsonValue, true); 

 
$userId = $Value['userId'] ?? null;


require_once 'config.php';

 $sql1 = "DELETE FROM tblusers WHERE userId = '$userId'";
   
     
   $sql2 = "DELETE FROM tblUsersAddresses WHERE useraddId = '$userId'";
   if (mysqli_query($con, $sql1)) {
            $response = array("message" => "deleted successfully", "status" => "success");
        } else {
            $response = array("message" => "error in deleting", "status" => "error");
        }


// Close the database connection
mysqli_close($con);

echo json_encode($response);

?>