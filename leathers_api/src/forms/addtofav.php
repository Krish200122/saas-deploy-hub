<?php

    header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$jsonCart = file_get_contents('php://input');
$Cart = json_decode($jsonCart);

require_once 'config.php';
$msg = "";

// Validate inputs
if (!isset($Cart->userId) || !isset($Cart->variantId)) {
    echo json_encode(['sts' => false, 'msg' => 'Missing userId or variantId']);
    exit;
}

$userId = intval($Cart->userId);
$variantId = intval($Cart->variantId);

// Check if favorite exists
$checkSQL = "SELECT * FROM tblfavorites WHERE favuserId = $userId AND favvariantId = $variantId";
$checkRes = mysqli_query($con, $checkSQL);

if (mysqli_num_rows($checkRes) == 0) {
    $res = mysqli_query($con, "SELECT IFNULL(MAX(favId),0)+1 AS MaxfavId FROM tblfavorites");
    $data = mysqli_fetch_array($res);
    $Fav_Id = $data['MaxfavId'];

    $insertSQL = "INSERT INTO tblfavorites(favId, favuserId, favvariantId) VALUES($Fav_Id, $userId, $variantId)";
    if (mysqli_query($con, $insertSQL)) {
        $msg = "New record created successfully. Last inserted ID is: $Fav_Id";
    } else {
        $msg = "Error: " . mysqli_error($con);
    }
} else {
    $deleteSQL = "DELETE FROM tblfavorites WHERE favuserId = $userId AND favvariantId = $variantId";
    if (mysqli_query($con, $deleteSQL)) {
        $msg = "Removed successfully.";
    } else {
        $msg = "Error: " . mysqli_error($con);
    }
}

// Return JSON response
echo json_encode(['sts' => true, 'msg' => $msg]);
?>