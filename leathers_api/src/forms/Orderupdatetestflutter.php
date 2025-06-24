<?php 

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once 'config.php';

$jsonValue = file_get_contents('php://input');
$jsonValue = html_entity_decode($jsonValue);
$Value = json_decode($jsonValue);

if ($Value === null) {
    http_response_code(400);
    die(json_encode(['message' => 'Invalid JSON input']));
}

$orderId = isset($Value->ordid) ? $Value->ordid : null;
$newAddId = isset($Value->addid) ? $Value->addid : null;
$newOrdDis = isset($Value->discount) ? $Value->discount : null;
$newOrderState = isset($Value->ordstatusid) ? $Value->ordstatusid : null;
$newOrdTotal = isset($Value->ordTotal) ? $Value->ordTotal : null;

if (!$orderId || !$newAddId || $newOrdDis === null || $newOrderState === null || $newOrdTotal === null) {
    http_response_code(400);
    die(json_encode(['message' => 'Missing required fields']));
}

if ($con->connect_error) {
    http_response_code(500);
    die(json_encode(['message' => 'Database connection failed: ' . $con->connect_error]));
}

// Update tblorders with new ordTotal
$stmt = $con->prepare("UPDATE `tblorders` SET `ordaddId` = ?, `ordDiscount` = ?, `ordState` = ?, `ordTotal` = ? WHERE `ordId` = ?");
$stmt->bind_param("ididi", $newAddId, $newOrdDis, $newOrderState, $newOrdTotal, $orderId);

if ($stmt->execute()) {
    $msg = "Order updated successfully. ";
} else {
    $msg = "Failed to update order: " . $stmt->error . ". ";
}

$stmt->close();

// Update tblorderdetails with new ordStatus
$stmt2 = $con->prepare("UPDATE `tblorderDetails` SET `ordStatus` = ? WHERE `orddetOrder` = ? AND `ordStatus` IN (0, 7)");
$stmt2->bind_param("ii", $newOrderState, $orderId);

if ($stmt2->execute()) {
    $msg .= "Order details updated successfully.";
} else {
    $msg .= "Failed to update order details: " . $stmt2->error;
}

$stmt2->close();
$con->close();

echo json_encode(['message' => $msg]);
?>
