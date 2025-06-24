<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$jsonReview = file_get_contents('php://input');
$jsonReview = html_entity_decode($jsonReview);
$Review = json_decode($jsonReview);

$usrId = $Review->{'usrId'};
$commentId = $Review->{'commentId'};
$replyText = $Review->{'reply'};
$replyId = $Review->{'replyId'};
require_once 'config.php';

$msg = "";

class Result
{

}

if ($replyId) {
    $sql = "DELETE FROM tblreplies WHERE replyId = $replyId and commentId = $commentId";

    if ($con->query($sql) === TRUE) {
        $msg = $msg . "Reply deleted successfully";
    } else {
        $msg = $msg . "Error deleting reply: " . $con->error;
    }
} else {
    //   $sql = "SELECT 1 from tblcomments where usrId =  '$usrId' and prdId ='$prdId'";

    // //$result = mysqli_num_rows(mysqli_query($con, $sql));
    
 
    // if (mysqli_num_rows(mysqli_query($con, $sql)) == 0) {
        $replyText = mysqli_real_escape_string($con, $replyText);
        $query = "SELECT IFNULL(MAX(replyId), 0) + 1 AS MaxReplyId FROM tblreplies";
        $res = mysqli_query($con, $query);
        $data = mysqli_fetch_array($res);
        $replyId = $data['MaxReplyId'];
    
        $sql = "INSERT INTO tblreplies (replyId, reply_usrId, reply_commentId, reply_text) VALUES ($replyId, '$usrId', $commentId, '$replyText')";
    
        if ($con->query($sql) === TRUE) {
            $msg = $msg . "Reply stored successfully";
        } else {
            $msg = $msg . "Error storing reply: " . $con->error;
        }
    // }else{
    //       $msg = $msg . "reply already exists!";
    // }
}

$result = new Result();
$result->prdId = $msg;

echo json_encode($msg);

?>
