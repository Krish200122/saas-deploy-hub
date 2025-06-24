<?php
   header("Access-Control-Allow-Origin: *");
    //open connection to mysql db
    require_once 'config.php';
    //fetch table rows from mysql db
    $sql = "SELECT * FROM `tblorders` LEFT JOIN tblorderStatus On ordstatusId = ordState LEFT JOIN tblusersAddresses On uadId = ordaddId WHERE ordState != 0 ORDER BY ordId ASC";
    $result = mysqli_query($con, $sql) or die("Error in Selecting " . mysqli_error($connection));
 
    //create an array
    $categoriesarray = array();
    while($row =mysqli_fetch_assoc($result))
    {
        $categoriesarray[] = $row;
    }
    echo json_encode($categoriesarray);
?>