<?php

header("Access-Control-Allow-Origin: *");

$url = $_SERVER['REQUEST_URI'];

$url_components = parse_url($url);

// Use parse_str() function to parse the
// string passed via URL
parse_str($url_components['query'], $params);

// Display result
$prdId = $params['prdId'];

//open connection to mysql db
require_once 'config.php';
//fetch table rows from mysql db
$sql = "SELECT prdId, prdName, prdCategory, prdImage, prdThampnail, prdPrice, prdDescription, catName , variantId, varcolorId, colorName, sizeName, varsizeId, varprdPrice, quantity, variantImage, variantThampnailImage, variantImage1, variantThampnailImage1, variantImage2, variantThampnailImage2 
        FROM tblproducts 
        LEFT JOIN tblproductvariant ON prdId = varprdId 
        LEFT JOIN tblcolors ON varcolorId = colorId 
        LEFT JOIN tblsize ON varsizeId = sizeId 
        LEFT JOIN tblcategories ON prdCategory = catId
        WHERE prdId = $prdId 
        ORDER BY variantId ASC"; // Added "ORDER BY variantId ASC" to sort by variantId

$result = mysqli_query($con, $sql) or die("Error in selecting product: " . mysqli_error($connection));

//create an array
$product = array();
while ($row = mysqli_fetch_assoc($result)) {
    $product[] = $row;
}

echo json_encode($product);

?>
