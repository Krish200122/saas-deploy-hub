<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Decode JSON input
$jsonValue = file_get_contents('php://input');
$jsonValue = html_entity_decode($jsonValue);
$Value = json_decode($jsonValue);

// Include DB config
require_once 'config.php';

$response = ["status" => "error", "message" => "Unknown error"];

if ($con->connect_error) {
    $response['message'] = "Connection failed: " . $con->connect_error;
} else {
    // Read coupon from input
    $coupon = $Value->coupon ?? null;

    if ($coupon) {
        // Prepare a statement to avoid SQL injection
        $stmt = $con->prepare("UPDATE tblcoupans SET usageCount = ? WHERE coupanCode = ?");
        
        // Set usageCount value (you can change 1 to any value if needed)
        $usageCount = 1;

        $stmt->bind_param("is", $usageCount, $coupon);

        if ($stmt->execute()) {
            $response = ["status" => "success", "message" => "Coupon updated successfully."];
        } else {
            $response['message'] = "Update failed: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $response['message'] = "Coupon code not provided.";
    }

    $con->close();
}

// Send response as JSON
echo json_encode($response);
?>
