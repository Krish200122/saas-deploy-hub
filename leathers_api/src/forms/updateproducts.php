<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
        
    $jsonValue = file_get_contents('php://input');
    $jsonValue = html_entity_decode($jsonValue);
    $Value = json_decode($jsonValue); 
    
        //open connection to mysql db
        require_once 'config.php';
    
    
              $sql = "UPDATE `tblproducts` SET `prdName`= '".$Value->{'productName'}."' ,`prdPrice`='".$Value->{'productPrice'}."',`prdDescription`='".$Value->{'productDescription'}."'  WHERE `prdId`='".$Value->{'productId'}."'";
        
              if ($con->query($sql) === TRUE) {
              
              $response[] = array('sts'=>false,'msg'=>'New record created successfully');
              } else {
              
              $response[] = array('sts'=>false,'msg'=>'"Error: " . $sql . "<br>" . $con->error');
            }    

   echo json_encode($response);
 ?>