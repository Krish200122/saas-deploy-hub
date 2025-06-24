<?php

    header("Access-Control-Allow-Origin: *");

    $url = $_SERVER['REQUEST_URI'];

    $url_components = parse_url($url);
    
    // Use parse_str() function to parse the
    // string passed via URL
    parse_str($url_components['query'], $params);
         
    // Display result
    $variantId = $params['variantId'];
    
    //open connection to mysql db
    require_once 'config.php';
    //fetch table rows from mysql db
    $sql = "SELECT prdId,prdName,prdsubcatId,prdMrp,CGST,SGST,prdDescription,variantId,varcolorId,colorName,sizeName,varsizeId,varprdMrp,stock,tblvariantproduct.fvoriginal,tblvariantproduct.fvthumbnail,tblvariantproduct.bvoriginal,tblvariantproduct.bvthumbnail,tblvariantproduct.tvoriginal,tblvariantproduct.tvthumbnail FROM tblproducts LEFT JOIN tblvariantproduct ON prdId = varprdId LEFT JOIN tblcolors ON varcolorId = colorId LEFT JOIN tblsize ON varsizeId = sizeId where variantId=".$variantId;
    $result = mysqli_query($con, $sql) or die("Error in select product." . mysqli_error($con));

    //create an array
    $product = array();
    while($row =mysqli_fetch_assoc($result))
    {
        $product[] = $row;
    }
    
    echo json_encode($product);

?>
