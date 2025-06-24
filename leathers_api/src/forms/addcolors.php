<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$jsonValue = file_get_contents('php://input');
$jsonValue = html_entity_decode($jsonValue);
$Value = json_decode($jsonValue, true); // Use true to decode as an associative array

$colorId = isset($Value['colorid']) ? $Value['colorid'] : null;
$colorname = $Value['color'];
$colorpalette = $Value['palette'];
require_once 'config.php';

$response = array(); // Initialize a response array

if ($colorId !== null) {
     $colorname = mysqli_real_escape_string($con, $colorname);
    $colorpalette = mysqli_real_escape_string($con, $colorpalette);
    // Update existing color
  $sql = "UPDATE tblcolors SET colorName = '$colorname', colorPalette = '$colorpalette' WHERE colorId = $colorId";

// Debugging: Check the final query
error_log("SQL Query: " . $sql);

if (mysqli_query($con, $sql)) {
    $response['message'] = "Color updated successfully";
    $response['status'] = "success";
} else {
    $response['message'] = "Error updating color: " . mysqli_error($con);
    $response['status'] = "error";
}
} else {
    // Check if the color name already exists
    $checkQuery = "SELECT colorId FROM tblcolors WHERE colorName = '$colorname'";
    $checkResult = mysqli_query($con, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $response['message'] = "Color name already exists";
        $response['status'] = "error";
    } else {
             $colorname = mysqli_real_escape_string($con, $colorname);
             $colorpalette = mysqli_real_escape_string($con, $colorpalette);
        // Insert a new color
        $query = "SELECT IFNULL(MAX(colorId), 0) + 1 AS MaxcolorId FROM tblcolors";
        $res = mysqli_query($con, $query);
        $data = mysqli_fetch_array($res);
        $MaxcolorId = $data['MaxcolorId'];

        $sql = "INSERT INTO tblcolors (colorId, colorName,colorPalette) VALUES ('$MaxcolorId', '$colorname', '$colorpalette')";

        if (mysqli_query($con, $sql)) {
            $response['message'] = "Color created successfully";
            $response['status'] = "success";
        } else {
            $response['message'] = "Error creating color: " . mysqli_error($con);
            $response['status'] = "error";
        }
    }
}

mysqli_close($con);

echo json_encode($response); // Send the response as JSON
?>
