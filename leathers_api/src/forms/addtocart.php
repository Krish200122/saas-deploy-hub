<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$jsonCart = file_get_contents('php://input');
$jsonCart = html_entity_decode($jsonCart);
$Cart = json_decode($jsonCart);

    $color =   $Cart->{'varcolorId'}  ;
    $size =  $Cart->{'varsizeId'}  ;
    $varId = $Cart->{'variantId'}  ;
    $ordId = $Cart->{'ordId'};

    require_once 'config.php';
$msg = "";

class Result
{

}

if ($Cart->{'ordId'} == 0) {

    $timestamp = date('Y-m-d H:i:s');

    $query = "SELECT ifNull(Max(ordId),0) + 1 As MaxOrdId from tblorders";

    $res = mysqli_query($con, $query);
    $data = mysqli_fetch_array($res);
    $Order_Id = $data['MaxOrdId'];


    $sql = "INSERT INTO tblorders(ordId,ordCustomer,ordDateTime,ordStatus,ordTotal,ordDiscount,ordSgst,ordCgst,transactionId,ordaddId) VALUES($Order_Id,0,'$timestamp',0," . $Cart->{'totalAmount'} . ",0,0,0,0,0)";

    if (mysqli_query($con, $sql)) {
         $msg = $msg . "New record created successfully. Last inserted ID is: " . $Order_Id;
    } else {
         $msg = $msg . "Error: " . $sql . "<br>" . $con->error;
    }

    $sql = "INSERT INTO tblorderDetails(orddetOrder,orddetProduct,orddetColor,orddetSize,orddetQty,orddetPrdPrice,orddetPrdMrp,orddetCgst,orddetSgst,orddetDiscount,orddetAmount) VALUES ($Order_Id,$varId,$color,$size," . $Cart->{'orddetQty'} . "," . $Cart->{'prdPrice'} . ",0,0,0,0," . $Cart->{'orddetQty'} * $Cart->{'prdPrice'} . ")";

} else {

    $Order_Id = $Cart->{'ordId'};

    $sql2 = "SELECT 1 from tblorderDetails where orddetOrder = " . $Order_Id . " and orddetProduct =".$varId;

    $result = mysqli_num_rows(mysqli_query($con, $sql2));
    
 
    if (mysqli_num_rows(mysqli_query($con, $sql2)) > 0) {
        $sql = "UPDATE tblorderDetails SET orddetQty=" . $Cart->{'orddetQty'} . ",orddetAmount=" . $Cart->{'orddetQty'} * $Cart->{'prdPrice'} . " where orddetOrder = " . $Order_Id . " and orddetProduct =" .$varId;
    } else {

        $sql = "INSERT INTO tblorderDetails(orddetOrder,orddetProduct,orddetColor,orddetSize,orddetQty,orddetPrdPrice,orddetPrdMrp,orddetCgst,orddetSgst,orddetDiscount,orddetAmount) VALUES ($Order_Id,$varId,$color,$size," . $Cart->{'orddetQty'} . "," . $Cart->{'prdPrice'} . ",0,0,0,0," . $Cart->{'orddetQty'} * $Cart->{'prdPrice'} . ")";
    }

}

if ($con->query($sql) === TRUE) {
     $msg = $msg . "Order detail created / updated successfully";
} else {
     $msg = $msg . "Error: " . $sql . "<br>" . $con->error;
}

$result = new Result();
$result->orderId = $Order_Id;

echo json_encode($Order_Id);

?>