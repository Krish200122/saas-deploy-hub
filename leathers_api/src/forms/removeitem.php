<?php

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");        
    
    $jsonCart = file_get_contents('php://input');
    $jsonCart = html_entity_decode($jsonCart);
    $Cart = json_decode($jsonCart);   
    
   $variant_Id = $Cart->{'variantId'};
    
   require_once 'config.php';
   $sql ="DELETE FROM tblorderDetails WHERE orddetProduct =".$variant_Id." and orddetOrder=".$Cart->{'ordId'};
    
   if ($con->query($sql) === TRUE) {
        
         $response[] = array('sts'=>true,'msg'=>'Successfully removed');
         
         
    }else{
        
         $response[] = array('sts'=>false,'msg'=>'error in remove item');
         
         
     } 
     echo json_encode($response);
?>