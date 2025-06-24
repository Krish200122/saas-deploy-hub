<?php
header("Access-Control-Allow-Origin: *");

$url = $_SERVER['REQUEST_URI'];

$url_components = parse_url($url);

// Use parse_str() function to parse the
// string passed via URL
parse_str($url_components['query'], $params);

// Set default value for $order if not provided
$order = isset($params['order']) ? $params['order'] : 'ASC';

//open connection to mysql db
require_once 'config.php';
//fetch table rows from mysql db
$sql = "SELECT prdId FROM tblproducts";

$result = mysqli_query($con, $sql) or die("Error in select products: " . mysqli_error($connection));

//create an array
$productsArray = array();

while ($row = mysqli_fetch_assoc($result)) {
    $productId = $row['prdId'];

    $con1 = mysqli_connect("localhost", "sqladmin1", "P@ssw0rd12345", "Ecommerce_Leathers") or die("Error in connection: " . mysqli_error($con));
    $sql1 = "SELECT prdId, prdName, prdCategory, prdImage, prdThampnail, prdPrice, prdDescription, catName , variantId, varcolorId, colorName, sizeName, varsizeId, varprdPrice
        FROM tblproducts 
        LEFT JOIN tblproductvariant ON prdId = varprdId 
        LEFT JOIN tblcolors ON varcolorId = colorId 
        LEFT JOIN tblsize ON varsizeId = sizeId 
        LEFT JOIN tblcategories ON prdCategory = catId
        WHERE prdId = $productId 
        ORDER BY prdPrice $order";

    $result1 = mysqli_query($con1, $sql1) or die("Error in selecting product: " . mysqli_error($con1));

    //create an array for each product
    $product = array();
    while ($row = mysqli_fetch_assoc($result1)) {
        $product[] = $row;
    }

    // Add the product array to the products array
    $productsArray[] = $product;
}

echo json_encode($productsArray);
?>
