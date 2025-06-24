<?php
header("Access-Control-Allow-Origin: *");

// Define database connection parameters
$host = "localhost";
$username = "sqladmin1";
$password = "P@ssw0rd12345";
$database = "Ecommerce_Leathers";

// Create a database connection
require_once 'config.php';

// Define the SQL query
$sql = "SELECT prdId, prdName, prdDiscount, tblproducts.fvoriginal, tblproducts.bvoriginal,prdPrice, prdMrp,tblsplOffers.sploffPrice,tblsplOffers.spldate   , MAX(variantId) AS variantId
        FROM tblproducts
        INNER JOIN tblvariantproduct ON varprdId = prdId AND prd_Status = 'true'
        LEFT JOIN tblsplOffers ON  variantId = splvarId
        GROUP BY prdId, prdName, tblproducts.fvoriginal, tblproducts.bvoriginal, prdMrp 
        ORDER BY prdId DESC
        LIMIT 4";

// Execute the SQL query
$result = mysqli_query($con, $sql);

// Check for query execution errors
if (!$result) {
    die("Error in select products: " . mysqli_error($con));
}

// Create an array to store the results
$productsArray = array();

// Fetch and store the results in the array
while ($row = mysqli_fetch_assoc($result)) {
    $productsArray[] = $row;
}

// Close the database connection
mysqli_close($con);

// Encode the array as JSON and echo it
echo json_encode($productsArray);
?>
