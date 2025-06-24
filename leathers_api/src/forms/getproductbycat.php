<?php

    header("Access-Control-Allow-Origin: *");

    $url = $_SERVER['REQUEST_URI'];

    $url_components = parse_url($url);
    
    // Use parse_str() function to parse the
    // string passed via URL
    parse_str($url_components['query'], $params);
         
    // Display result
    $subcatId = $params['subcatId'];
    
    //open connection to mysql db
    require_once 'config.php';
    //fetch table rows from mysql db
       $sql ="SELECT prdId,prdCategory,prdName,prdImage,prdPrice, subcatId, subcatName,subcatCat  FROM tblsubcategories JOIN tblproducts On prdCategory = subcatId where subcatId =".$subcatId ;
   
    $result = mysqli_query($con, $sql) or die("Error in Selecting " . mysqli_error($connection));

    //create an array
    $productsarray = array();
    while($row =mysqli_fetch_assoc($result))
    {
        $productsarray[] = $row;
    }
    
    echo json_encode($productsarray);
    
?>