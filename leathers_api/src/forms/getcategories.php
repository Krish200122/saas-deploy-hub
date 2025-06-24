<?php

    header("Access-Control-Allow-Origin: *");
    
       $url = $_SERVER['REQUEST_URI'];

    $url_components = parse_url($url);
    
    // Use parse_str() function to parse the
    // string passed via URL
    parse_str($url_components['query'], $params);
         
    // Display result
    $admin = $params['admin'];
    
    //open connection to mysql db
    require_once 'config.php';
    //fetch table rows from mysql db
    if($admin == 1){
    $sql = "SELECT * FROM `tblcategories`";
    }else{
        $sql = "SELECT * FROM `tblcategories` WHERE status = 'true'" ;
    }
    $result = mysqli_query($con,$sql) or die("Error in select products." . mysqli_error($con));

    //create an array
    $categoriesarray = array();
    while($row =mysqli_fetch_assoc($result))
    {
        $categoriesarray[] = $row;
    }
    
    echo json_encode($categoriesarray);
    
?>