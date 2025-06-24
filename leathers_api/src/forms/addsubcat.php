<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$jsonValue = file_get_contents('php://input');
$jsonValue = html_entity_decode($jsonValue);
$Value = json_decode($jsonValue, true); // Use true to decode as an associative array

$subcatId = isset($Value['subcatId']) ? $Value['subcatId'] : null;
$subcategoryname = $Value['subcategory'];
$subcatcat = $Value['CatId'];
$status  = $Value['status'];

require_once 'config.php';

$response = array(); // Initialize a response array

if ($subcatId !== null) {
    // Update existing color
    $sql = "UPDATE tblsubcategories SET subcatName = '$subcategoryname', status = '$status' WHERE subcatId = $subcatId";

    if (mysqli_query($con, $sql)) {
        $response['message'] = "Subcategory updated successfully";
        $response['status'] = "success";
    } else {
        $response['message'] = "Error updating Subcategory: " . mysqli_error($con);
        $response['status'] = "error";
    }
} else {
    // Check if the category name already exists
    $checkQuery = "SELECT subcatId FROM tblsubcategories WHERE subcatName = '$subcategoryname' AND subcatCat = '$subcatcat' ";
    $checkResult = mysqli_query($con, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $response['message'] = "Subcategory  already exists";
        $response['status'] = "error";
    } else {
        // Insert a new category
        $query = "SELECT IFNULL(MAX(subcatId), 0) + 1 AS MaxsubcatId FROM tblsubcategories";
        $res = mysqli_query($con, $query);
        $data = mysqli_fetch_array($res);
        $MaxsubcatId = $data['MaxsubcatId'];

        $sql = "INSERT INTO tblsubcategories (subcatId,subcatCat, subcatName,status) VALUES ('$MaxsubcatId','$subcatcat', '$subcategoryname','true')";

        if (mysqli_query($con, $sql)) {
            $response['message'] = "Subcategory created successfully";
            $response['status'] = "success";
        } else {
            $response['message'] = "Error creating Subcategory: " . mysqli_error($con);
            $response['status'] = "error";
        }
    }
}

mysqli_close($con);

echo json_encode($response); // Send the response as JSON
?>
