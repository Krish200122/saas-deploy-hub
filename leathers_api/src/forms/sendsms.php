<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
$jsonSMS = file_get_contents('php://input');
$jsonSMS = html_entity_decode($jsonSMS);
$SMS = json_decode($jsonSMS);

$mobileNo = $SMS->{'mobileno'};
$msg = $SMS->{'message'};
echo $ordStatus;
//open connection to MySQL DB
require_once 'config.php';

$authKey = "402097AlFfBHjNvh64be1a40P1";
$senderId = "TAMU";
$mobileNumber = $mobileNo;
$message = urlencode($msg);
//$route = "default";
$postData = array(
    'authkey' => $authKey,
    'mobiles' => $mobileNumber,
    'message' => $message,
    'sender' => $senderId,
    //'route' => $route
);
$url = "http://api.msg91.com/api/sendhttp.php";
$ch = curl_init($url);
curl_setopt_array($ch, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postData
    //,CURLOPT_FOLLOWLOCATION => true
));
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$response = curl_exec($ch); // <-- Missing semicolon here
//Print error if any
if (curl_errno($ch)) {
    echo 'error: ' . curl_error($ch);
}

curl_close($ch);
echo $response; // <-- Missing semicolon here

?>
