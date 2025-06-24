<?php
    
    header("Access-Control-Allow-Origin: *");

    $url = $_SERVER['REQUEST_URI'];

    $url_components = parse_url($url);
    
    // Use parse_str() function to parse the
    // string passed via URL
    parse_str($url_components['query'], $params);
         
    // Display result
    $userId = $params['userId'];
    
    require_once 'config.php';
    //fetch table rows from mysql db
    $sql = "SELECT * FROM `tblfavorites` 
    LEFT JOIN tblvariantproduct ON  favvariantId = variantId LEFT JOIN tblproducts ON  prdId = varprdId WHERE favuserId=".$userId;
    
    $result = mysqli_query($con, $sql) or die("Error in select products." . mysqli_error($connection));

    //create an array
    $productsarray = array();

    while($row =mysqli_fetch_assoc($result))
    {
        $productsarray[] = $row;
    }

    echo json_encode($productsarray);

?>