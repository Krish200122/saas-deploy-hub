<?php

   header("Access-Control-Allow-Origin: *");

    $url = $_SERVER['REQUEST_URI'];

    $url_components = parse_url($url);

    // Use parse_str() function to parse the
    // string passed via URL
    parse_str($url_components['query'], $params);
         
    // Display result
    $SubcatId = $params['subcatId'];
    
    //open connection to mysql db
    require_once 'config.php';
     //fetch table rows from mysql db 
   $sql = "DELETE  FROM `tblsubcategories` where subcatId =".$SubcatId;
  
     if ($con->query($sql) === TRUE) {
     
         $response[] = array('sts'=>true,'msg'=>'Successfully removed');
         
    }else{
        
         $response[] = array('sts'=>false,'msg'=>'error in remove item');
         
     } 
     echo json_encode($response);
    
?>