<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
        
    $jsonValue = file_get_contents('php://input');
    $jsonValue = html_entity_decode($jsonValue);
    $Value = json_decode($jsonValue); 
    
        //open connection to mysql db
        require_once 'config.php';
    $msg="";
    
    class Result{
    
    }
     
    $ordId = $Value->{'orderId'};
    
     
    if ($con->connect_error) {
      die("Connection failed: " . $con->connect_error);
    }
        $sql = "UPDATE `tblorders` SET `ordTotal`='".$Value->{'amount'}."',`ordaddId`='".$Value->{'selectedaddId'}."',`ordCustomer`='".$Value->{'UserId'}."' WHERE `ordId`='".$Value->{'orderId'}."'";
        
        if ($con->query($sql) === TRUE) {
              $msg= $msg."Order detail created / updated successfully" ;
            } else {
             $msg=$msg. "Error: " . $sql . "<br>" . $con->error ;
        }
        
    $result = new Result();
    $result->orderId= $Order_Id;

    echo json_encode($Order_Id);
        
   
 ?>