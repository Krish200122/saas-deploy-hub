<?php
 
header("Access-Control-Allow-Origin: *");
 
//open connection to mysql db
require_once 'config.php';
//fetch table rows from mysql db
$sql = "SELECT
    fvoriginal,
    prdMrp,
    prdPrice,
    prdName,
    prdId,
    specprdId
FROM tblproducts
LEFT JOIN tblspecification ON specprdId = prdId
GROUP BY prdId";
$result = mysqli_query($con, $sql) or die("Error in select products: " . mysqli_error($con));
 
//create arrays to hold products and specifications
$productsArray = array();
$specificationsArray = array();
 
// Fetching combined data
while ($row = mysqli_fetch_assoc($result)) {
    $productsArray[] = array(
        'fvoriginal' => $row['fvoriginal'],
        'prdMrp' => $row['prdMrp'],
        'prdPrice' => $row['prdPrice'],
        'prdName' => $row['prdName'],
        'prdId' => $row['prdId'],
        'specprdId' => $row['specprdId']
    );
}
 
 
echo json_encode($productsArray);
 
// Close the connection
mysqli_close($con);