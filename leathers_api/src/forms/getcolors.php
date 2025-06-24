<?php
    
    header("Access-Control-Allow-Origin: *");
    
    
    //open connection to mysql db
    require_once 'config.php';
    //mysqli_select_db("Ecommerce_Aquatics",$con);
    //fetch table rows from mysql db
    $sql = "SELECT * FROM `tblcolors`" ;
    $result = mysqli_query($con,$sql) or die("Error in select products." . mysqli_error($connection));

    //create an array
    $productsarray = array();

     while($row =mysqli_fetch_assoc($result))
    {
        $productsarray[] = $row;
    }

    echo json_encode($productsarray);

?>