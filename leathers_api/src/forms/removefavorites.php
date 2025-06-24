<?php
header("Access-Control-Allow-Origin: *");

    $url = $_SERVER['REQUEST_URI'];

    $url_components = parse_url($url);
// Open connection to MySQL database
    require_once 'config.php';
    // Use parse_str() function to parse the
    // string passed via URL
    parse_str($url_components['query'], $params);

// Check if 'userId' or 'favoriteId' parameter is present
if (isset($_GET['userId'])) {
    // Get the 'userId' parameter from the URL
    $userId = intval($_GET['userId']); // Sanitize input to prevent SQL injection

    

    // Use prepared statement to prevent SQL injection
    $sql = "DELETE FROM `tblfavorites` WHERE favuserId = ?";
} elseif (isset($_GET['favoriteId'])) {
    // Get the 'favoriteId' parameter from the URL
    $favoriteId  = intval($_GET['favoriteId']); // Sanitize input to prevent SQL injection

    // Use prepared statement to prevent SQL injection
    $sql = "DELETE FROM `tblfavorites` WHERE favId = ?";
} else {
    // Handle the case where neither parameter is provided
    $response = array('sts' => false, 'msg' => 'Missing or invalid parameter: userId or favoriteId');
}

// Check if a valid SQL query was defined
if (isset($sql)) {
    // Create a prepared statement
    $stmt = mysqli_prepare($con, $sql);

    if ($stmt) {
        if (isset($userId)) {
            // Bind the 'userId' parameter
            mysqli_stmt_bind_param($stmt, "i", $userId);
        } elseif (isset($favoriteId)) {
            // Bind the 'favoriteId' parameter
            mysqli_stmt_bind_param($stmt, "i", $favoriteId);
        }

        // Execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            $response = array('sts' => true, 'msg' => 'Successfully removed');
        } else {
            $response = array('sts' => false, 'msg' => 'Error in removing item: ' . mysqli_error($con));
        }

        // Close the prepared statement
        mysqli_stmt_close($stmt);
    } else {
        $response = array('sts' => false, 'msg' => 'Error in preparing the statement');
    }
}

// Close the database connection
mysqli_close($con);

// Output the response as JSON
echo json_encode($response);
?>
