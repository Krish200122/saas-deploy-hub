<?php
    header("Access-Control-Allow-Origin: *");

    $url = $_SERVER['REQUEST_URI'];

    $url_components = parse_url($url);

    // Use parse_str() function to parse the
    // string passed via URL
    parse_str($url_components['query'], $params);
         
    // Display result
     $Userid = $params['userid'];
    
    //open connection to mysql db
    require_once 'config.php';
    //fetch table rows from mysql db
    $sql ="SELECT prdId,prdName,prdCategory,prdImage,prdPrice,prdMrp,orddetOrder,orddetQty,ordStatus FROM tblorderDetails LEFT JOIN tblproducts ON orddetProduct = prdId LEFT JOIN tblorders ON orddetOrder = ordId WHERE (ordStatus= 6 || ordStatus= 0) and ordCustomer = $Userid" ;
   
    $result = mysqli_query($con, $sql) or die("Error in Selecting " . mysqli_error($connection));

    //create an array
    $cartarray = array();
    while($row =mysqli_fetch_assoc($result))
    {
        $cartarray[] = $row;
    }
    
    echo json_encode($cartarray);
?>