<?php
   
    header("Access-Control-Allow-Origin: *");
 
 
    //open connection to mysql db
    require_once 'config.php';
//mysqli_select_db("Ecommerce_Aquatics",$con);
//fetch table rows from mysql db
$sql = "SELECT sizeId, sizesubcatId, sizeName, subcatName
FROM tblsize
LEFT JOIN tblsubcategories ON subcatId = sizesubcatId
ORDER BY
CASE
    WHEN sizeName REGEXP '^[0-9]+$' THEN 0
    ELSE 1
END,
  CASE sizeName
    WHEN 'XS' THEN 1
    WHEN 'S' THEN 2
    WHEN 'M' THEN 3
    WHEN 'L' THEN 4
    WHEN 'XL' THEN 5
    WHEN 'XXL' THEN 6
    WHEN 'XXXL' THEN 7
    ELSE 99
END,
  CAST(sizeName AS UNSIGNED);
" ;
$result = mysqli_query($con, $sql) or die("Error in select products.".mysqli_error($connection));
 
 
$productsarray = array();
 
while ($row = mysqli_fetch_assoc($result)) {
  $productsarray[] = $row;
}
 
    echo json_encode($productsarray);
 
?>