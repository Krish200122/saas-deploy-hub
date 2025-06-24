<?php
 
 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
 
$jsonCart = file_get_contents('php://input');
$jsonCart = html_entity_decode($jsonCart);
$Cart = json_decode($jsonCart);
 
 
$color     = isset($Cart->varcolorId) ? intval($Cart->varcolorId) : 0;
$size      = isset($Cart->varsizeId) ? intval($Cart->varsizeId) : 0;
$varId     = isset($Cart->variantId) ? intval($Cart->variantId) : 0;
$Order_Id  = isset($Cart->ordId) ? intval($Cart->ordId) : 0;
$userId    = isset($Cart->userId) ? intval($Cart->userId) : 0;
$status    = isset($Cart->ordStatus) ? intval($Cart->ordStatus) : 0;
$prdMrp    = isset($Cart->varprdMrp) ? intval($Cart->varprdMrp) : 0;
$cgst      = isset($Cart->CGST) ? intval($Cart->CGST) : 0;
$sgst      = isset($Cart->SGST) ? intval($Cart->SGST) : 0;
$ordQty    = isset($Cart->orddetQty) ? intval($Cart->orddetQty) : 1;
$prdPrice  = isset($Cart->varprdPrice) ? intval($Cart->varprdPrice) : 0;
$ordAmt    = $ordQty * $prdPrice;
 
 
require_once 'config.php';
$msg = "";
 
 
class Result {}
 
 
if ($Order_Id == 0) {
   $orderResult = CreateOrder();
   $msg = $orderResult['msg'];
   $Order_Id = $orderResult['Order_Id'];
} else {
   if ($userId) {
       $sql4 = "UPDATE tblorders SET ordCustomer=$userId WHERE ordId = $Order_Id";
       $msg = ($con->query($sql4) === TRUE) ? "UserId added successfully" : "Error in adding userId";
   }
 
 
   if ($Order_Id && $varId) {
       $sql = "SELECT 1 FROM tblorderDetails WHERE orddetOrder = $Order_Id AND orddetProduct = $varId";
       $exists = mysqli_num_rows(mysqli_query($con, $sql));
 
 
       if ($exists > 0 && $status > 0) {
           $sql5 = "SELECT 1 FROM tblorderDetails WHERE orddetOrder = $Order_Id AND orddetProduct = $varId AND ordStatus = $status";
           if (mysqli_num_rows(mysqli_query($con, $sql5)) > 0) {
               $sql2 = "UPDATE tblorderDetails SET 
                           orddetQty = $ordQty,
                           orddetAmount = $ordAmt,
                           orddetPrdMrp = $prdMrp,
                           orddetCgst = $cgst,
                           orddetSgst = $sgst 
                        WHERE orddetOrder = $Order_Id AND orddetProduct = $varId AND ordStatus = $status";
               $msg = ($con->query($sql2) === TRUE) 
                       ? "In tblorderDetails buynow/special item is updated successfully"
                       : "Error in updating buynow/special item in tblorderDetails";
           } else {
               $orderResult = CreateOrder();
               $msg = $orderResult['msg'];
               $Order_Id = $orderResult['Order_Id'];
           }
       } elseif ($exists > 0) {
           $sql6 = "UPDATE tblorderDetails SET 
                       orddetQty = $ordQty,
                       orddetAmount = $ordAmt,
                       orddetPrdMrp = $prdMrp,
                       orddetCgst = $cgst,
                       orddetSgst = $sgst 
                    WHERE orddetOrder = $Order_Id AND orddetProduct = $varId";
           $msg = ($con->query($sql6) === TRUE) 
                   ? "In tblorderDetails cart product updated successfully"
                   : "Error in updating cart product tblorderDetails";
       } else {
           $statusVal = $status ?: 0;
           $sql3 = "INSERT INTO tblorderDetails (
                       orddetOrder, orddetProduct, orddetColor, orddetSize,
                       orddetQty, orddetPrdPrice, orddetPrdMrp, orddetCgst,
                       orddetSgst, orddetAmount, ordStatus
                   ) VALUES (
                       $Order_Id, $varId, $color, $size,
                       $ordQty, $prdPrice, $prdMrp, $cgst,
                       $sgst, $ordAmt, $statusVal
                   )";
           $msg = ($con->query($sql3) === TRUE) 
                   ? (($status > 0) 
                       ? "Using orderId for buynow product, Order detail is created successfully"
                       : "Using orderId Order detail created successfully for cart product")
                   : "Error in creating order detail: $sql3 <br> " . $con->error;
       }
   }
}
 
 
function CreateOrder() {
   global $con, $Cart, $userId, $status, $color, $size, $varId, $prdMrp, $cgst, $sgst;
 
 
   $ordQty = isset($Cart->orddetQty) ? intval($Cart->orddetQty) : 1;
   $prdPrice = isset($Cart->varprdPrice) ? intval($Cart->varprdPrice) : 0;
   $ordAmt = $ordQty * $prdPrice;
   $timestamp = date('Y-m-d H:i:s');
 
 
   $res = mysqli_query($con, "SELECT IFNULL(MAX(ordId),0) + 1 AS MaxOrdId FROM tblorders");
   $data = mysqli_fetch_array($res);
   $Order_Id = $data['MaxOrdId'];
 
 
   $sqlOrder = "INSERT INTO tblorders (
                   ordId, ordCustomer, ordDateTime, ordTotal,
                   ordDiscount, transactionId, ordaddId, ordState
                ) VALUES (
                   $Order_Id, $userId, '$timestamp', 0, 0, 0, 0, 0
                )";
 
 
   $statusVal = ($status > 0) ? $status : 0;
   $sqlDetail = "INSERT INTO tblorderDetails (
                   orddetOrder, orddetProduct, orddetColor, orddetSize,
                   orddetQty, orddetPrdPrice, orddetPrdMrp,
                   orddetCgst, orddetSgst, orddetAmount, ordStatus
                ) VALUES (
                   $Order_Id, $varId, $color, $size,
                   $ordQty, $prdPrice, $prdMrp,
                   $cgst, $sgst, $ordAmt, $statusVal
                )";
 
 
   if (mysqli_query($con, $sqlOrder)) {
       $msg = "New order created successfully. Order ID: $Order_Id";
       if (mysqli_query($con, $sqlDetail)) {
           $msg .= ($status > 0)
               ? " | Order details record created successfully for buynow item."
               : " | Order details record created successfully for cart product.";
       } else {
           $msg = "Error inserting order details: $sqlDetail";
       }
   } else {
       $msg = "Error creating order: $sqlOrder";
   }
 
 
   return [
       'msg' => $msg,
       'Order_Id' => $Order_Id
   ];
}
 
 
$result = new Result();
$result->orderId = $Order_Id;
 
 
$response = [
   'msg' => $msg,
   'OrderId' => $Order_Id
];
echo json_encode($response);
?>