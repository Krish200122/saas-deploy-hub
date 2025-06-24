<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$jsonReview = file_get_contents('php://input');
$jsonReview = html_entity_decode($jsonReview);
$Review = json_decode($jsonReview);

$usrId = $Review->{'usrId'};
$prdId = $Review->{'prdId'};
$varId = $Review->{'varId'};
$commentText = $Review->{'feedback'};
$commentId = $Review->{'commentId'};


require_once 'config.php';

$msg = "";

class Result {}

if ($commentId) {
    $sql = "DELETE FROM tblcomments WHERE commentId = $commentId ";

    if ($con->query($sql) === TRUE) {
        $msg = $msg . "Comment deleted successfully";
    } else {
        $msg = $msg . "Error deleting comment: " . $con->error;
    }
} else {
    $sql = "SELECT 1 from tblcomments where usrId =  '$usrId' and varId ='$varId'";

    if (mysqli_num_rows(mysqli_query($con, $sql)) == 0) {
        $commentText = mysqli_real_escape_string($con, $commentText);
        
        $query = "SELECT IFNULL(MAX(commentId), 0) + 1 AS MaxCommentId FROM tblcomments";
        $res = mysqli_query($con, $query);
        $data = mysqli_fetch_array($res);
        $commentId = $data['MaxCommentId'];
        
        $currentDateTime = date('Y-m-d H:i:s');
        $sql = "INSERT INTO tblcomments (commentId, usrId, prdId,varId, comment_text, date_time) 
                VALUES ($commentId, $usrId, $prdId,$varId, '$commentText', '$currentDateTime')";
    
        if ($con->query($sql) === TRUE) {
            $msg = $msg . "Comment stored successfully";
        } else {
            $msg = $msg . "Error storing comment: " . $con->error;
        }
    } else {
         $msg = $msg . "Comment already exists!";
    }
}

$result = new Result();
$result->prdId = $msg;

echo json_encode($msg);

?>
