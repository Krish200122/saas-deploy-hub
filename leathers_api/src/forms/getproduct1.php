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
$sql = "SELECT variantId FROM tblvariantproduct";

$result1 = mysqli_query($con, $sql) or die("Error in select products: " . mysqli_error($connection));

$variantId = array();

while($row1 = mysqli_fetch_assoc($result1)){
    $variantId[]= $row1;
}
$product = array();
    foreach ($variantId as $varid){
        
        $var = $varid['variantId'];
        
    $sql1 = "SELECT prdId, prdName, prdsubcatId, tblproducts.fvoriginal, tblproducts.bvoriginal, prdPrice, prdDescription, catName , variantId, varcolorId, colorName, sizeName, varprdPrice, prdDiscount
        FROM tblvariantproduct 
        LEFT JOIN tblproducts ON  prdId = varprdId
        LEFT JOIN tblcolors ON  colorId = varcolorId
        LEFT JOIN tblsize ON  sizeId = varsizeId
        LEFT JOIN tblcategories ON prdsubcatId = catId
        WHERE variantId = $var 
        ORDER BY variantId $order";
    
    $result1 = mysqli_query($con, $sql1) or die("Error in selecting product: " . mysqli_error($con));

    //create an array for each product
    
    while ($row = mysqli_fetch_assoc($result1)) {
        $product[] = $row;
        
    }
    
    

}
//echo json_encode($product);
    // Add the product array to the products array
//$array = json_decode($product, true);

// Group the objects by prdId
$groupedArray = [];
foreach ($product as $item) {
    $prdId = $item['prdId'];
    if (!isset($groupedArray[$prdId])) {
        $groupedArray[$prdId] = [];
    }
    $groupedArray[$prdId][] = $item;
}

// Convert the grouped array back to indexed array
$finalArray = array_values($groupedArray);

// Encode the final array as JSON
$finalJsonArray = json_encode($finalArray);

echo $finalJsonArray;
    

?>
