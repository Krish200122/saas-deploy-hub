<?php

    header("Access-Control-Allow-Origin: *");
    
    //open connection to mysql db
    require_once 'config.php';
    //fetch table rows from mysql db
    $sql = "SELECT * FROM `tblorderStatus`";
    $result = mysqli_query($con, $sql) or die("Error in select orderstatus." . mysqli_error($connection));

    //create an array
    $categoriesarray = array();
    while($row =mysqli_fetch_assoc($result))
    {
        $orderstatusarray[] = $row;
    }
    
    echo json_encode($orderstatusarray);
    
?>