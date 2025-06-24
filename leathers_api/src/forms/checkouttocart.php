<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
$jsonCart = file_get_contents('php://input');
$jsonCart = html_entity_decode($jsonCart);
$Cart = json_decode($jsonCart);
 
$preordId = $Cart->{'preordId'};
$ordId = $Cart->{'ordId'};
$variantId = $Cart->{'variantId'};
$ordStatus = $Cart->{'ordStatus'};
$quantity = $Cart->{'quantity'};
$price = $Cart->{'Price'};
$colorId = $Cart->{'colorid'};
$sizeId = $Cart->{'sizeid'};
 
require_once 'config.php'; // DB connection
 
class Result {}
 
$msg = "";
 
$getsql = "SELECT * FROM tblorderDetails WHERE orddetOrder = $preordId AND orddetProduct = $variantId";
$result = $con->query($getsql);
 
if ($result->num_rows > 0) {
    // Variant exists - update quantity and status
    $updateSql = "UPDATE tblorderDetails
                  SET orddetQty = orddetQty + $quantity, ordStatus = $ordStatus
                  WHERE orddetOrder = $preordId AND orddetProduct = $variantId";
    if ($con->query($updateSql) === TRUE) {
        $msg = "Quantity updated for existing product in cart.";
    } else {
        $msg = "Error updating quantity: " . $con->error;
    }
} else {
 
    $removeSql = "DELETE FROM tblorderDetails WHERE orddetOrder = $ordId";
    if ($con->query($removeSql) === TRUE) {
        $msg = "Removed existing product from cart.";
    } else {
        $msg = "Error removing existing product: " . $con->error;
    }

  $removesqltblorders = "DELETE FROM tblorders WHERE ordId = $ordId";
    if ($con->query($removesqltblorders) === TRUE) {
        $msg = "Removed existing order from tblorders.";
    } else {
        $msg = "Error removing existing order: " . $con->error;
    }
    // Variant doesn't exist - insert new row
   $insertSql = "INSERT INTO tblorderDetails (orddetOrder,orddetProduct,orddetColor,orddetSize,orddetQty,orddetPrdPrice,orddetPrdMrp,orddetCgst,orddetSgst,orddetAmount,ordStatus)
                  VALUES ($preordId, $variantId, $colorId, $sizeId, $quantity ,$price, 0 ,0,0, $quantity * $price, $ordStatus)";
    if ($con->query($insertSql) === TRUE) {
        $msg = "New product added to cart.";
    } else {
        $msg = "Error inserting product: " . $con->error;
    }
}
 
echo json_encode($msg);
?>