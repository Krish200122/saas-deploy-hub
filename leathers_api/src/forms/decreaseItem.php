<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$jsonCart = file_get_contents('php://input');
$jsonCart = html_entity_decode($jsonCart);
$Cart = json_decode($jsonCart);

$variant_Id = $Cart->{'variantId'};

require_once 'config.php';
$SQL = "SELECT * FROM tblorderDetails WHERE orddetProduct = $variant_Id AND orddetOrder = " . $Cart->{'ordId'};

$result = $con->query($SQL);

if ($row = $result->fetch_assoc()) {
    $qty = $row["orddetQty"];
} else {
    echo json_encode(["sts" => false]);
    exit;
}

if ($qty > 1) {
    $ordetQty = ($qty - 1);
    $sql = "UPDATE tblorderDetails SET orddetQty = $ordetQty WHERE orddetProduct = $variant_Id AND orddetOrder = " . $Cart->{'ordId'};
} else {
    $sql = "DELETE FROM tblorderDetails WHERE orddetProduct = $variant_Id AND orddetOrder = " . $Cart->{'ordId'};
    $SQL = "DELETE FROM tblorders WHERE ordId = " . $Cart->{'ordId'};
}

if ($con->query($sql) === TRUE) {
    echo json_encode(["sts" => true]);
} else {
    echo json_encode(["sts" => false]);
}

?>
