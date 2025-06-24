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
    $sql = "SELECT ordTotal FROM tblorders WHERE ordId=".$ordId;
    //$result = mysqli_query($con, $sql) or die("Error in select product." . mysqli_error($connection));

   $result = $con->query($sql);

if ($row = $result->fetch_assoc()) {
    $amount = $row["ordTotal"] ;
  }
    
    echo json_encode($amount);
    
?>