<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Get userId from URL
$url = $_SERVER['REQUEST_URI'];
$url_components = parse_url($url);
parse_str($url_components['query'], $params);

if (!isset($params['userId'])) {
    echo json_encode(["success" => false, "message" => "User ID is required"]);
    exit;
}

$userid = intval($params['userId']);

require_once 'config.php';

// Start transaction
mysqli_begin_transaction($con);

try {
    // Delete from tblUsers
    $sql1 = "DELETE FROM tblUsers WHERE userId = $userid";
    $stmt1 = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($stmt1, "i", $userid);
    mysqli_stmt_execute($stmt1);

    // Check if any rows were affected
    if (mysqli_stmt_affected_rows($stmt1) === 0) {
        throw new Exception("User not found.");
    }

    // Delete from tblUsersAddresses
    $sql2 = "DELETE FROM tblUsersAddresses WHERE uadId = $userid";
    $stmt2 = mysqli_prepare($con, $sql2);
    mysqli_stmt_bind_param($stmt2, "i", $userid);
    mysqli_stmt_execute($stmt2);

    // Commit transaction
    mysqli_commit($con);

    echo json_encode(["success" => true, "message" => "Account deleted successfully"]);
} catch (Exception $e) {
    mysqli_rollback($con);
    echo json_encode(["success" => false, "message" => "Error deleting account: " . $e->getMessage()]);
}

// Close connection
mysqli_close($con);

?>
