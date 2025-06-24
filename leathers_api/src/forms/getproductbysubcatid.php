<?php
header("Access-Control-Allow-Origin: *");
 
$url = $_SERVER['REQUEST_URI'];
 
$url_components = parse_url($url);
 
// Use parse_str() function to parse the
// string passed via URL
parse_str($url_components['query'], $params);
 
$catId = isset($params['catId']) ? $params['catId'] : null; // Make sure $catId is defined
 
// Set default value for $order if not provided
$order = isset($params['order']) ? $params['order'] : 'ASC';
 
//open connection to mysql db
require_once 'config.php';
//fetch table rows from mysql db
$sql = "SELECT variantId FROM tblvariantproduct";
 
$result1 = mysqli_query($con, $sql) or die("Error in select products: " . mysqli_error($con));
 
$variantId = array();
 
while($row1 = mysqli_fetch_assoc($result1)){
    $variantId[]= $row1;
}
$product = array();
    foreach ($variantId as $varid){
       
        $var = $varid['variantId'];
       
        //$con1 = mysqli_connect("localhost", "sqladmin1", "P@ssw0rd12345", "Ecommerce_Leathers") or die("Error in connection: " . mysqli_error($connection));
    $sql1 = "SELECT p.prdId, p.prdName, p.prdsubcatId, p.prdPrice, p.prdMrp,p.SGST,p.CGST ,p.status, p.fvoriginal, p.bvoriginal, p.prdDescription,v.prdDiscount, v.variantId, v.varcolorId, c.colorName, s.sizeName, v.varprdPrice, v.varprdMrp,v.vartaxPrice, spl.sploffPrice, spl.spldate
        FROM tblvariantproduct v
        LEFT JOIN tblproducts p ON p.prdId = v.varprdId AND p.status = 'true'
        LEFT JOIN tblcolors c ON c.colorId = v.varcolorId
        LEFT JOIN tblsize s ON s.sizeId = v.varsizeId
        LEFT JOIN tblcategories cat ON p.prdsubcatId = cat.catId
        LEFT JOIN tblsplOffers spl ON v.variantId = spl.splvarId
        WHERE v.variantId = $var AND p.prdsubcatId = $catId
        ORDER BY v.variantId $order";
   
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