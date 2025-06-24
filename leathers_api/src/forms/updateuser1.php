<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
$folderPath = "../assets/Profile/";

// $jsonValue = file_get_contents('php://input');
$postdata = file_get_contents("php://input");
$request = json_decode($postdata, true); 
// $jsonValue = html_entity_decode($jsonValue);
// $Value = json_decode($jsonValue);

// Create connection
require_once 'config.php';
$name = $request['name'];
$lastname = $request['lastname'];
$userphone = $request['phone'];
$userid = $request['id'];
$email = $request['email'];
$password = $request['password'];
$hash = password_hash($password,PASSWORD_DEFAULT);
    $image_parts = explode(";base64,", $request['image']);
   
    $image_base64 = base64_decode($image_parts[1]);
    $image_extension = explode('/', mime_content_type($image_parts[0]))[1];
    $image_name = preg_replace('/\s+/', '_', $request['name']); // Using 'title' from the request
    $file = $folderPath . $image_name . '.' . jpg;
    file_put_contents($file, $image_base64);

  $sql = "UPDATE tblusers SET userName = '$name',userLastName='$lastname', userPhoneno = '$userphone', userImage ='http://88.222.244.141:98/leathers/assets/Profile/$image_name.jpg' WHERE userId = '$userid'";
  
  if($email && $password){
       $sql = "UPDATE tblusers SET userPassword = '$hash' WHERE userEmail = '$email'";
  }
  if (mysqli_query($con, $sql)) {
         $response['message'] = "userdetails updated successfully";
            $response['status'] = "success";
        
    } else {
         $response['message'] = "Error: " . $sql . "<br>" . mysqli_error($con);
        $response['status'] = "error";
    }


echo json_encode($response);