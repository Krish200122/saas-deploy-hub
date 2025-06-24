<?php

   header("Access-Control-Allow-Origin: *");

    $url = $_SERVER['REQUEST_URI'];

    $url_components = parse_url($url);

    // Use parse_str() function to parse the
    // string passed via URL
    parse_str($url_components['query'], $params);
         
    // Display result
    $prdId = $params['prdId'];
    
    //open connection to mysql db
    require_once 'config.php';
     //fetch table rows from mysql db 
   $sql = "SELECT * FROM tblspecification where specprdId =".$prdId;
    $result = mysqli_query($con, $sql) or die("Error in select product." . mysqli_error($connection));

    //create an array
    $user = array();
    while($row =mysqli_fetch_assoc($result))
    {
        $user[] = $row;
    }
    
    echo json_encode($user);
    
?>