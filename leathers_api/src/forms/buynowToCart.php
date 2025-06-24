<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
$jsonCart = file_get_contents('php://input');
$jsonCart = html_entity_decode($jsonCart);
$Cart = json_decode($jsonCart);
 
    $ordId = $Cart->{'ordId'};
    $variantId = $Cart->{'variantId'};
    $ordStatus = $Cart->{'ordStatus'};
        //open connection to mysql db
        require_once 'config.php';
   
class Result
{
 
}
 
    $sql = "UPDATE tblorderDetails SET ordStatus = $ordStatus WHERE orddetOrder = $ordId and orddetProduct = $variantId";
   
    if ($con->query($sql) === TRUE) {
          $msg = "Buynow product is added to addtocart product ";
        } else {
          $msg ="Error in adding Buynow product to addtocart product";
    }
$result = new Result();
echo json_encode($msg);