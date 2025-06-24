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
    $sql = "SELECT *  FROM tblorders  WHERE ordStatus = 1 AND ordCustomer=".$userId;
    $results = $con->query($sql);
   
        $resultset = array();
        while ($row = mysqli_fetch_assoc($results)) {
        $resultset[] = $row;
            }

        // $resultset now holds all rows from the first query.
        foreach ($resultset as $result){
            
            $ordId = $result['ordId'];
            
            echo $ordId ;
            
            $SQL = "UPDATE tblorders SET ordStatus = 5 WHERE ordId=".$ordId;
            
     if ($con->query($SQL) === TRUE) {
              $msg = "Order canceled successfully";
            } else {
              $msg = "Error: " . $SQL . "<br>" . $con->error;
          }
           $response[] = array("Message" => $msg);

echo json_encode($response);
        }

?>