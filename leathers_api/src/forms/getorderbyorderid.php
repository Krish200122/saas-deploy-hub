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
   $sql = "SELECT * FROM tblorders LEFT JOIN tblorderDetails ON orddetOrder = ordId and ordStatus != 0 LEFT JOIN tblvariantproduct ON variantId = orddetProduct LEFT JOIN tblproducts ON prdId = varprdId LEFT JOIN tblsize ON sizeId = orddetSize LEFT JOIN tblcolors ON colorId = orddetColor WHERE ordId=".$ordId;

    $result = mysqli_query($con, $sql) or die("Error in Selecting " . mysqli_error($connection));

    //create an array
    $categoriesarray = array();
    while($row =mysqli_fetch_assoc($result))
    {
        $categoriesarray[] = $row;
    }
    echo json_encode($categoriesarray);
    
?>