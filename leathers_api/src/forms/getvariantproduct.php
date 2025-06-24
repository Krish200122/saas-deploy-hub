<?php
    
    header("Access-Control-Allow-Origin: *");
    
    //open connection to mysql db
    require_once 'config.php';
    //fetch table rows from mysql db
    $sql = "SELECT * FROM `tblvariantproduct` LEFT JOIN tblproducts ON  varprdId = prdId";
    $result = mysqli_query($con,$sql) or die("Error in select products." . mysqli_error($connection));

    //create an array
    $productsarray = array();

     while($row =mysqli_fetch_assoc($result))
    {
        $productsarray[] = $row;
    }

    echo json_encode($productsarray);

?>