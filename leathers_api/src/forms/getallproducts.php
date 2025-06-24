<?php
 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
 
require_once 'config.php';
 
$sql = "SELECT
            p.fvoriginal,
            p.prdName,
            p.prdId,
            p.prdsubcatId,
            tv.prdDiscount,
            c.catId,
            c.catName,
            sc.subcatName,
            tv.variantId,
            tv.vartaxPrice,
            tv.varprdPrice AS prdPrice,
            tv.varprdMrp AS prdMrp
        FROM
            tblproducts p
        LEFT JOIN
            tblsubcategories sc ON p.prdsubcatId = sc.subcatId
        LEFT JOIN
            tblcategories c ON sc.subcatCat = c.catId
        LEFT JOIN
            (
                SELECT varprdId, MIN(variantId) AS variantId
                FROM tblvariantproduct
                GROUP BY varprdId
            ) AS minvar ON minvar.varprdId = p.prdId
        LEFT JOIN
            tblvariantproduct tv ON tv.variantId = minvar.variantId
        WHERE
            p.status = 'TRUE'";
 
$result = mysqli_query($con, $sql) or die("Error in select products. " . mysqli_error($con));
 
$productsarray = array();
 
while ($row = mysqli_fetch_assoc($result)) {
    $productsarray[] = $row;
}
 
echo json_encode($productsarray);
?>