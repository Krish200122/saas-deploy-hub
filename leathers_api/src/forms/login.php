<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
$jsonValue = file_get_contents('php://input');
$jsonValue = html_entity_decode($jsonValue);
$value = json_decode($jsonValue);
 
$UserEmail = isset($value->email) ? trim($value->email) : '';
$UserPW = isset($value->password) ? $value->password : '';
 
require_once 'config.php';
 
$response = [];
$UserId = null;
 
// Sanitize input
$UserEmail = mysqli_real_escape_string($con, $UserEmail);
 
// Prepare and execute SQL
$sql = "SELECT userId, userPassword FROM tblusers WHERE userEmail = '$UserEmail'";
$result = mysqli_query($con, $sql);
 
if (!$result) {
    http_response_code(500);
    echo json_encode(["Message" => "Database error", "error" => mysqli_error($con)]);
    exit;
}
 
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $UserId = $row['userId'];
    $hashedPassword = $row['userPassword'];
 
    if (password_verify($UserPW, $hashedPassword)) {
        $response = [
            "Message" => 200,
            "userId" => $UserId
        ];
    } else {
        $response = [
            "Message" => 'Incorrect password', // Incorrect password
            "userId" => null
        ];
    }
} else {
    $response = [
        "Message" => 'Email not found', // Email not found
        "userId" => null
    ];
}
 
echo json_encode($response);
?>