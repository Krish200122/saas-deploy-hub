<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$jsonCart = file_get_contents('php://input');
$jsonCart = html_entity_decode($jsonCart);
$Cart = json_decode($jsonCart);

$variant_Id = $Cart->{'variantId'};
$product_Id = $Cart->{'productId'};

require_once 'config.php';
// Delete from tblvariant where the variantId matches
$sql1 = "DELETE FROM tblvariantproduct WHERE variantId = '$variant_Id'";

// Execute the first query
if(mysqli_query($con, $sql1)) {
    // After the first delete, now delete from tblproducts
    $sql2 = "DELETE FROM tblproducts WHERE prdId = '$product_Id'";

    if(mysqli_query($con, $sql2)) {
        echo json_encode(["status" => "success", "message" => "Records deleted successfully from both tblvariantproduct and tblproducts."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error deleting record from tblproducts: " . mysqli_error($con)]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Error deleting record from tblvariantproduct: " . mysqli_error($con)]);
}

mysqli_close($con);

?>