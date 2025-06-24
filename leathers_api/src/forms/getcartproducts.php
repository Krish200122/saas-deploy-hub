<?php
    header("Access-Control-Allow-Origin: *");

    $url = $_SERVER['REQUEST_URI'];

    $url_components = parse_url($url);

    // Use parse_str() function to parse the
    // string passed via URL
    parse_str($url_components['query'], $params);
         
    // Display result
     $ordId = $params['ordId'];
    
    //open connection to mysql db
    require_once 'config.php';
    //fetch table rows from mysql db
    $sql ="SELECT variantId,variantImage,varprdPrice,varsizeId,varcolorId,orddetOrder,orddetQty,ordStatus,prdName,colorName,sizeName FROM tblorderDetails LEFT JOIN tblproductvariant ON orddetProduct = variantId LEFT JOIN tblproducts ON prdId = varprdId LEFT JOIN tblcolors ON colorId = varcolorId LEFT JOIN tblsize ON sizeId = varsizeId LEFT JOIN tblorders ON orddetOrder = ordId WHERE (ordStatus= 6 || ordStatus= 0) and ordId = $ordId" ;
    // $sql ="SELECT ordId,orddetQty,ordStatus,variantId,variantImage,varprdPrice,varsizeId,varcolorId,prdName,colorName,sizeName FROM tblorders1 LEFT JOIN tblorderDetails1 ON orddetOrder = ordId LEFT JOIN tblproductvariant ON variantId= orddetProduct LEFT JOIN tblproducts ON prdId = varprdId LEFT JOIN tblcolors ON colorId = orddetColor LEFT JOIN tblsize ON sizeId = orddetSize WHERE ordId = $ordId";
    $result = mysqli_query($con, $sql) or die("Error in Selecting " . mysqli_error($con));

    //create an array
    $cartarray = array();
    while($row =mysqli_fetch_assoc($result))
    {
        $cartarray[] = $row;
    }
    
    echo json_encode($cartarray);
?>