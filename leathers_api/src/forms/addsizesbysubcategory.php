<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$jsonSize = file_get_contents('php://input');
$jsonSize = html_entity_decode($jsonSize);
$Size = json_decode($jsonSize, true);

require_once 'config.php';// Retrieve the data sent from the Angular application
$subCategoryId = $Size['subcategoryid'];
$sizeArray = explode(',', $Size['size']);

// Initialize the response array
$response = array();

// Loop through the sizes array and insert each size as a separate row
foreach ($sizeArray as $sizeValue) {
    // Trim whitespace from the size value (optional)
    $sizeValue = trim($sizeValue);

    // Check if the sizeName already exists for the given sizesubcatId
    $checkSql = "SELECT sizeName FROM tblsize WHERE sizesubcatId = ? AND sizeName = ?";
    $checkStmt = mysqli_prepare($con, $checkSql);
    mysqli_stmt_bind_param($checkStmt, "is", $subCategoryId, $sizeValue);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);

    if (mysqli_stmt_num_rows($checkStmt) > 0) {
        // Size already exists, skip insertion
        $response = array('status' => 'error', 'message' => 'Size ' . $sizeValue . ' already exists for this subcategory');
    } else {
        // Size does not exist, proceed with the insertion
        $query = "SELECT IFNULL(MAX(sizeId), 0) + 1 AS MaxsizeId FROM tblsize";
        $res = mysqli_query($con, $query);
        $data = mysqli_fetch_array($res);
        $size_Id = $data['MaxsizeId'];

        // Insert the size
        $insertSql = "INSERT INTO tblsize (sizeId, sizesubcatId, sizeName) VALUES (?, ?, ?)";
        $insertStmt = mysqli_prepare($con, $insertSql);
        mysqli_stmt_bind_param($insertStmt, "iis", $size_Id, $subCategoryId, $sizeValue);

        if (mysqli_stmt_execute($insertStmt)) {
            // Insert successful
            $response = array('status' => 'success', 'message' => 'Data inserted successfully');
        } else {
            // Insert failed
            $response = array('status' => 'error', 'message' => 'Error while inserting data: ' . mysqli_stmt_error($insertStmt));
            break; // Exit the loop on the first error
        }

        // Close the insert statement
        mysqli_stmt_close($insertStmt);
    }

    // Close the check statement
    mysqli_stmt_close($checkStmt);
}

// Close the database connection
mysqli_close($con);

echo json_encode($response);
?>
