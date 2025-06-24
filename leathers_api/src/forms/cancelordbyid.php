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
            
    $SQL = "UPDATE tblorders SET ordState = 5 WHERE ordId=".$ordId;
            
     if ($con->query($SQL) === TRUE) {
              $msg = "Order canceled successfully";
            } else {
              $msg = "Error: " . $SQL . "<br>" . $con->error;
            }
            
    $response[] = array("Message" => $msg);

echo json_encode($response);
        

?>