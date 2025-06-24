<?php
header("Access-Control-Allow-Origin: *");

$url = $_SERVER['REQUEST_URI'];
$url_components = parse_url($url);
parse_str($url_components['query'], $params);
$varprdid = $params['prdId'];

require_once 'config.php';
$sql = "SELECT v.variantId, v.varprdPrice, v.varprdMrp, v.vartaxPrice, v.prdDiscount, v.stock, v.fvoriginal, v.bvoriginal, v.tvoriginal, v.svoriginal, v.avoriginal, v.fvthumbnail, v.bvthumbnail, v.tvthumbnail, v.svthumbnail, v.avthumbnail,v.prd_status, p.prdId, p.prdName, p.CGST,p.SGST, p.HSN, p.prdDescription, p.status, s.sizeId, s.sizeName, s.sizesubcatId, c.colorId, c.colorName, t.catId, t.catName, u.subcatId, u.subcatName, u.subcatCat FROM tblvariantproduct v LEFT JOIN tblproducts p ON p.prdId = v.varprdId 
      LEFT JOIN tblsize s ON s.sizeId = v.varsizeId
      LEFT JOIN tblcolors c ON c.colorId = v.varcolorId  
      LEFT JOIN tblsubcategories u ON u.subcatId = p.prdsubcatId 
      LEFT JOIN tblcategories t ON t.catId = u.subcatCat 
      WHERE v.varprdId =" . $varprdid;

$result = mysqli_query($con, $sql) or die("Error in select product: " . mysqli_error($con));

$user = array();
while ($row = mysqli_fetch_assoc($result)) {
    $user[] = $row;
}

echo json_encode($user);
?>
