<?php

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");        
    
    $jsonValue = file_get_contents('php://input');
    $jsonValue = html_entity_decode($jsonValue);
    $value = json_decode($jsonValue);     
    
    require_once 'config.php';

    $sql = "SELECT userId,userEmail FROM tblusers  where userEmail = '".$value->{'email'}."'";
    $result = $con->query($sql);

    if ($row = $result->fetch_assoc()) {
        
      $UserId = $row["userId"] ;
      $UserEmail = $row["userEmail"];
      
    } 

    $SQL = "SELECT uadAddressline1 FROM tblusersAddresses WHERE uadEmail = '".$value->{'email'}."'";
    $exeSQL = mysqli_query($con, $SQL);
    $checkPhone =  mysqli_num_rows($exeSQL);

    $result = $con->query($SQL);

    if ($row = $result->fetch_assoc()) {
    
        $add_1 = $row["uadAddressline1"] ;
    }
        
    echo $uadId_Id;

    $sqlcount="SELECT * from tblusersAddresses where uadAddressline1 ='".$value->{'add1'}."'";
    
    echo "  uadEmail = '$UserEmail' ";
     
    if(mysqli_num_rows(mysqli_query($con,$sqlcount)) > 0){
                     
                $sqll ="UPDATE tblusersAddresses SET   uadPhoneno = '".$value->{'phone'}."',uadUserName = '".$value->{'firstname'}."',uadEmail = '".$value->{'email'}."',uadAddressline1 = '".$value->{'add1'}."',uadAddressline2 = '".$value->{'add2'}."',uadDistrict = '".$value->{'city'}."',uadPincode ='".$value->{'zipcode'}."'
                WHERE uadAddressline1 ='".$value->{'add1'}."'" ;
                
                $username = "UPDATE tblusers SET userName ='".$value->{'firstname'}."', userPhoneno ='".$value->{'phone'}."' WHERE userId = '$UserId'";   
                
                if ($con->query($sqll) == TRUE) { 

                    $Message = "Customer detail created / updated successfully 2";
                    
                } else {
                    
                      $Message = "Something Went Wrong ";
                      
                }
                
    }else 
    {
        
                $query ="SELECT ifNull(Max(uadId),0) + 1 As MaxuadId from tblusersAddresses";
    
                $res =mysqli_query($con,$query);
                $data =mysqli_fetch_array($res);
                $uadId_Id = $data['MaxuadId'];        
    
                $newuser ="INSERT INTO tblusersAddresses (uadId,useraddId,uadUserName,uadEmail,uadAddressline1,uadAddressline2,uadDistrict,uadPhoneno,uadPincode)
                VALUES ($uadId_Id,$UserId,'".$value->{'firstname'}."','".$value->{'email'}."','".$value->{'add1'}."','".$value->{'add2'}."','".$value->{'city'}."','".$value->{'phone'}."','".$value->{'zipcode'}."')";
                 
                 //$username = "UPDATE tblusers SET userName = '".$value->{'firstname'}."', userPhoneno='".$value->{'phone'}."' WHERE userId = '$UserId'";
                 
                if ($con->query($newuser) == TRUE) { 
                    
                            $Message = "Customer detail created / updated successfully 2";
                    
                }
                
                else {
                    
                      $Message = "Error in creating user details";
                      
                     }

    } 
   
$response[] = array("Message" => $Message);

echo json_encode($response);