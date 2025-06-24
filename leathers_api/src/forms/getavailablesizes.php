<?php

    header("Access-Control-Allow-Origin: *");

    $url = $_SERVER['REQUEST_URI'];

    $url_components = parse_url($url);
    
    // Use parse_str() function to parse the
    // string passed via URL
    parse_str($url_components['query'], $params);
         
    // Display result
    $colorId  = $params['colorId'];
    $prdId   = $params['prdId'];
    
    
    //open connection to mysql db
    require_once 'config.php';
    //fetch table rows from mysql db
     $sql = "SELECT sizeName,sizeId,variantId,stock,varprdPrice FROM tblvariantproduct LEFT JOIN tblcolors ON varcolorId = colorId LEFT JOIN tblsize ON varsizeId = sizeId where varprdId = $prdId AND varcolorId = $colorId";
    $result = mysqli_query($con, $sql) or die("Error in select product." . mysqli_error($connection));

    //create an array
    $sizes = array();
    while($row =mysqli_fetch_assoc($result))
    {
        $sizes[] = $row;
    }
    
    echo json_encode($sizes);

?>
