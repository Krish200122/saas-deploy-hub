<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$jsonRating = file_get_contents('php://input');
$jsonRating = html_entity_decode($jsonRating);
$Rating = json_decode($jsonRating);

    $usrId =   $Rating->{'usrId'}  ;
    $prdId =  $Rating->{'prdId'}  ;
    $varId = $Rating->{'varId'};
    $rating = $Rating->{'rating'}  ;

    require_once 'config.php';
$msg = "";

class Result
{

}

    $sql = "SELECT 1 from tblrating where usrId =  '$usrId' and varId ='$varId'";

    $result = mysqli_num_rows(mysqli_query($con, $sql));
    
 
    if (mysqli_num_rows(mysqli_query($con, $sql)) > 0) {
        $sql = "UPDATE tblrating SET rating = $rating where usrId =  '$usrId' and varId ='$varId'";
    } else {

        $sql = "INSERT INTO tblrating(usrId,prdId,varId,rating) VALUES ('$usrId','$prdId','$varId','$rating')";
    }



if ($con->query($sql) === TRUE) {
     $msg = $msg . "Rating created / updated successfully";
} else {
     $msg = $msg . "Error: " . $sql . "<br>" . $con->error;
}

$result = new Result();
$result->prdId = $msg;

echo json_encode($msg);

?>