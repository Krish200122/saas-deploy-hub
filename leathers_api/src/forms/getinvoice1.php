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
$sql = "SELECT ordId,ordCustomer,ordDateTime,ordDiscount,ordState,ordTotal,transactionId, userName, userEmail, uadAddress, uadCountry, uadState, uadDistrict, uadPincode, uadPhoneno FROM tblorders LEFT JOIN tblusers ON userId = ordCustomer LEFT JOIN tblusersAddresses ON uadId = ordaddId WHERE ordId= $ordId";

$result = mysqli_query($con, $sql) or die("Error in Selecting " . mysqli_error($connection));

if (!$result) {
    die("Error in Selecting: " . mysqli_error($con));
}
// Create an array
$categoriesarray = array();
while ($row = mysqli_fetch_assoc($result)) {
    $categoriesarray[] = $row;
}

$sql1 = "SELECT orddetQty,orddetPrdPrice AS salePrice,orddetCgst,orddetSgst,orddetAmount,orddetPrdMrp,prdName,varprdMrp as prdPrice,sploffPrice FROM tblorderDetails LEFT JOIN tblvariantproduct ON variantId= orddetProduct LEFT JOIN tblsplOffers ON splvarId= orddetProduct LEFT JOIN tblproducts ON prdId = varprdId WHERE orddetOrder = $ordId";
$result1 = mysqli_query($con, $sql1) or die("Error in Selecting " . mysqli_error($connection));

// Create an array
$categoriesarray1 = array();
while ($row1 = mysqli_fetch_assoc($result1)) {
    $categoriesarray1[] = $row1;
}

// Add a new key-value pair to $categoriesarray
$categoriesarray[0]['prddetail'] = $categoriesarray1; // Assuming there is only one row in $categoriesarray

 echo json_encode($categoriesarray);

?>
