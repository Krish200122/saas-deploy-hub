<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$jsonValue = file_get_contents('php://input');
$jsonValue = html_entity_decode($jsonValue);

$Value = json_decode($jsonValue);


// Create connection
require_once 'config.php';

$userpw = $Value->{'password'};
$username = $Value->{'userName'};
// $userImage = $Value->{'userImage'};
$hash = password_hash($userpw,PASSWORD_DEFAULT);

// Check if email already exists
$email = $Value->{'email'};
$emailQuery = "SELECT * FROM tblusers WHERE userEmail = '$email'";
$emailResult = mysqli_query($con, $emailQuery);

if (mysqli_num_rows($emailResult) > 0) {
    $msg = "Email id already exists";
} else {
  
    // Get the maximum userId
    $query = "SELECT IFNULL(MAX(userId), 0) + 1 AS MaxUserId FROM tblusers";
    $res = mysqli_query($con, $query);
    $data = mysqli_fetch_array($res);
    $MaxUserId = $data['MaxUserId'];
    

    // Insert new user details into tblusers
    $sql = "INSERT INTO tblusers(userId, userName, userEmail, userPassword, IsAdmin,userLastName,userImage,userPhoneno,userDefaultaddress)
            VALUES('".$MaxUserId."', '".$username."', '".$email."', '".$hash."', false,'','','','')";
            
       
    if (mysqli_query($con, $sql)) {
       $msg = "User detail created/updated successfully";
       $statusCode = 200;
    } else {
        $statusCode = 400;
        $msg = "error in registering";
    }

}
$response[] = array("msg" => $msg,"statusCode" => $statusCode,"UserId" => $MaxUserId );
    
echo json_encode($response);
mysqli_close($con);
?>
