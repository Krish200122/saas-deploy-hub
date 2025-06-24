<?php
header("Access-Control-Allow-Origin: *");

// Get the 'admin' parameter from the URL
$admin = $_GET['admin'];

// Open connection to MySQL db
require_once 'config.php';
if ($admin == 1) {
    $sql ="SELECT splId,splvarId AS variantId,spldate,sploccasion,sploffPrice AS varprdPrice,sizeName, prdId, prdName, prdsubcatId, tblproducts.fvoriginal, tblproducts.bvoriginal, prdPrice AS prdMrp, prdDescription, catName, varcolorId, colorName,varsizeId,  prdPrice FROM tblsplOffers LEFT JOIN tblvariantproduct on variantId = splvarId LEFT JOIN tblproducts ON  prdId = varprdId LEFT JOIN tblcolors ON  colorId = varcolorId LEFT JOIN tblsize ON  sizeId = varsizeId LEFT JOIN tblcategories ON prdsubcatId = catId ORDER BY splId ASC ";
    // $sql = "SELECT * FROM `tblsploffers`";
} else {
    // Format the current date to match the database format
    $currentDate = date('d/m/Y');
    $currentDateFormatted = mysqli_real_escape_string($con, $currentDate);
     $sql ="SELECT splId,splvarId AS variantId,spldate,sploccasion,sploffPrice AS varprdPrice, prdId, prdName, prdsubcatId, tblproducts.fvoriginal, tblproducts.bvoriginal, prdPrice AS prdMrp, prdDescription, catName, varcolorId, colorName,varsizeId, sizeName, prdPrice FROM tblsplOffers LEFT JOIN tblvariantproduct on variantId = splvarId LEFT JOIN tblproducts ON  prdId = varprdId LEFT JOIN tblcolors ON  colorId = varcolorId LEFT JOIN tblsize ON  sizeId = varsizeId LEFT JOIN tblcategories ON prdsubcatId = catId WHERE STR_TO_DATE(`spldate`, '%d/%m/%Y') >= STR_TO_DATE('$currentDateFormatted', '%d/%m/%Y') ORDER BY splId ASC";
    // $sql = "SELECT * FROM `tblsploffers` WHERE STR_TO_DATE(`date`, '%d/%m/%Y') >= STR_TO_DATE('$currentDateFormatted', '%d/%m/%Y')";
}

$result = mysqli_query($con, $sql) or die("Error in select products." . mysqli_error($con));

// Create an array
$productsarray = array();

while ($row = mysqli_fetch_assoc($result)) {
    $productsarray[] = $row;
}

echo json_encode($productsarray);

mysqli_close($con);
?>
