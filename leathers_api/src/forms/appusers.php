<?php

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
        
    $jsonValue = file_get_contents('php://input');
    $jsonValue = html_entity_decode($jsonValue);
    $Value = json_decode($jsonValue);   

    // Create connection
    require_once 'config.php';
    $userpw=md5($Value ->{'password'});
    
    if($value ->{'email'} == 'userEmail')
    {
     echo'email id already exists';
     
  
    }
        else {
            
            $query ="SELECT ifNull(Max(userId),0) + 1 As MaxUserId from tblusers";
                $res =mysqli_query($con,$query);
                $data =mysqli_fetch_array($res);
                $MaxUserId = $data['MaxUserId'];
                        
                $sql ="INSERT INTO tblusers (userId,userName,userEmail,userPhoneno,userPassword,IsAdmin)
                VALUES ('".$MaxUserId."','".$Value ->{'name'}."','".$Value -> {'email'}."','".$Value -> {'phone'}."','$userpw',false)";
                 
                 
                if ($con->query($sql) === TRUE) { 
                echo "User detail created / updated successfully";
            } else {
               echo "Error: " . $sql . "<br>" . $con->error;
            }
            
    }
	
?>
