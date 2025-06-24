<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once 'config.php';

// Parse query string
$url = $_SERVER['REQUEST_URI'];
$url_components = parse_url($url);
parse_str($url_components['query'], $params);

// Validate and sanitize input
if (isset($params['coupanId']) && isset($params['userId'])) {
    $coupanId = intval($params['coupanId']);
    $userId = intval($params['userId']); // Ensure it's an integer

    // Prepare and execute the update query
    $updateSql = "UPDATE tblcoupans SET scratch = 1, userId = ? WHERE coupanId = ?";
    $stmt = $con->prepare($updateSql);
    $stmt->bind_param("ii", $userId, $coupanId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Coupon updated with user ID."]);
    } else {
        echo json_encode(["success" => false, "message" => "Update failed.", "error" => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Missing 'coupanId' or 'userId' parameter."]);
}

$con->close();
?>
