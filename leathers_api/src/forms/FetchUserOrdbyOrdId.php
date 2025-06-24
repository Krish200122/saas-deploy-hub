<?php
header("Access-Control-Allow-Origin: *");
 
$url = $_SERVER['REQUEST_URI'];
 
$url_components = parse_url($url);
 
// Use parse_str() function to parse the
// string passed via URL
parse_str($url_components['query'], $params);
 
// Display result
$ordId = $params['ordId'];
 
require_once 'config.php';
//fetch table rows from MySQL DB
$sql = "SELECT o.ordCustomer, o.ordTotal,o.ordDiscount,o.ordDateTime,o.ordState,o.transactionId,e.ordstatusDescription, d.orddetOrder,
d.orddetPrdPrice, d.orddetProduct, d.orddetQty, v.variantId, v.varprdPrice, v.fvoriginal, p.prdId,p.prdName, p.prdPrice,s.sizeName,n.colorName,c.comment_text, r.rating FROM tblorders o LEFT JOIN tblorderDetails d ON d.orddetOrder = o.ordId
LEFT JOIN tblorderStatus e ON e.ordstatusId = o.ordState LEFT JOIN tblusers u ON u.userId = o.ordCustomer LEFT JOIN tblvariantproduct v
ON v.variantId = d.orddetProduct LEFT JOIN tblproducts p ON p.prdId = v.varprdId LEFT JOIN tblsize s ON s.sizeId = v.varsizeId 
LEFT JOIN tblcolors n ON n.colorId = v.varcolorId LEFT JOIN tblcomments c ON c.varId = d.orddetProduct AND c.usrId = o.ordCustomer  LEFT JOIN tblrating r ON
r.varId = d.orddetProduct and r.usrId = o.ordCustomer
WHERE o.ordId = $ordId";
 
$result = mysqli_query($con, $sql) or die("Error in Selecting " . mysqli_error($con));
 
//create an array
$categoriesarray = array();
while ($row = mysqli_fetch_assoc($result)) {
    $categoriesarray[] = $row;
}
echo json_encode($categoriesarray);
?>