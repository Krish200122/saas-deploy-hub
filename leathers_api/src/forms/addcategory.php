<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$jsonValue = file_get_contents('php://input');
$jsonValue = html_entity_decode($jsonValue);
$Value = json_decode($jsonValue, true); // Use true to decode as an associative array

$catId = isset($Value['catid']) ? $Value['catid'] : null;
$categoryname  = $Value['category'];
$status  = $Value['status'];

require_once 'config.php';

$response = array(); // Initialize a response array

if ($catId !== null) {
    // Update existing color
    $sql = "UPDATE tblcategories SET catName = '$categoryname', status = '$status' WHERE catId = $catId";

    if (mysqli_query($con, $sql)) {
        $response['message'] = "Category updated successfully";
        $response['status'] = "success";
    } else {
        $response['message'] = "Error updating category: " . mysqli_error($con);
        $response['status'] = "error";
    }
} else {
    // Check if the category name already exists
    $checkQuery = "SELECT catId FROM tblcategories WHERE catName = '$categoryname'";
    $checkResult = mysqli_query($con, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $response['message'] = "category  already exists";
        $response['status'] = "error";
    } else {
        // Insert a new category
        $query = "SELECT IFNULL(MAX(catId), 0) + 1 AS MaxcatId FROM tblcategories";
        $res = mysqli_query($con, $query);
        $data = mysqli_fetch_array($res);
        $MaxcatId = $data['MaxcatId'];

        $sql = "INSERT INTO tblcategories (catId, catName,status) VALUES ('$MaxcatId', '$categoryname','true')";

        if (mysqli_query($con, $sql)) {
            $response['message'] = "Category created successfully";
            $response['status'] = "success";
        } else {
            $response['message'] = "Error creating Category: " . mysqli_error($con);
            $response['status'] = "error";
        }
    }
}

mysqli_close($con);

echo json_encode($response); // Send the response as JSON
?>
