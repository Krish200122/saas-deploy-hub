<?php

$encodedData = file_get_contents('php://input');  // take data from react native fetch API
$decodedData = json_decode($encodedData, true);

$UserEmail = $decodedData['email'];
$UserName = $decodedData['name'];
$UserPhone = $decodedData['phone'];
$UserPW = ($decodedData['password']); //password is hashed

require_once 'config.php';

 $SQL = "SELECT * FROM tblusers WHERE userEmail = '$UserEmail'";
 $exeSQL = mysqli_query($con, $SQL);
 $checkEmail =  mysqli_num_rows($exeSQL);
// // $SQL = "SELECT * FROM tblusers WHERE token = '$Token'";
// // $exeSQL = mysqli_query($con, $SQL);
// // $checkToken =  mysqli_num_rows($exeSQL);

$result = $con->query($SQL);
if ($row = $result->fetch_assoc()) {
        
      $UserId = $row["userId"] ;
   
      
    }

 if($checkEmail != 0){
     $Message = "Already registered.$UserPhone";
     $Query = "UPDATE  tblusers SET (userName ='$UserName',userPhoneno = '$UserPhone' ) WHERE userEmail = '$UserEmail'";
 } 
 else {

     $query ="SELECT ifNull(Max(userId),0) + 1 As MaxUserId from tblusers";
                $res =mysqli_query($con,$query);
                $data =mysqli_fetch_array($res);
                $MaxUserId = $data['MaxUserId'];
                   
                $sql ="INSERT INTO tblusers (userId,userName,userEmail,userPassword,userPhoneno,isAdmin)
                VALUES ('$MaxUserId','$UserName','$UserEmail','$UserPW','$UserPhone',false)";
                 
                if ($con->query($sql) === TRUE) { 
                    
                $Message = "Customer detail created / updated successfully";
                    
                }
                else {
                      $Message = "Error";
                     }
 }
$response[] = array("Message" => $Message,"userId" => $UserId);

echo json_encode($response);
