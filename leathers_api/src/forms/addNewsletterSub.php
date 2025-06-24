<?php

header("Access-Control-Allow-Origin: *");
 $url = $_SERVER['REQUEST_URI'];

$url_components = parse_url($url);

// Use parse_str() function to parse the
// string passed via URL
parse_str($url_components['query'], $params);
     
// Display result
$emailId = $params['emailId'];
require_once 'config.php';
$msg = "";

class Result
{

}

    $sql = "SELECT 1 from tblnewsletter where email =  '$emailId'";

    $result = mysqli_num_rows(mysqli_query($con, $sql));
    
 
    if (mysqli_num_rows(mysqli_query($con, $sql)) > 0) {
          $msg = $msg . "Gosh!, you already subscribed!";
    } else {
        $query = "SELECT IFNULL(MAX(SNo), 0) + 1 AS MaxSNo FROM tblnewsletter";
        $res = mysqli_query($con, $query);
        $data = mysqli_fetch_array($res);
        $Sno = $data['MaxSNo'];
        $sql = "INSERT INTO tblnewsletter(SNo,email) VALUES ($Sno,'$emailId')";
    }



if ($con->query($sql) === TRUE) {
     $msg = $msg . "Congrats, From now onwards you will get Updates from Our Website!";
} else {
     $msg = $msg . "Error: " . $sql . "<br>" . $con->error;
}

$result = new Result();
$result->prdId = $msg;

echo json_encode($msg);

?>