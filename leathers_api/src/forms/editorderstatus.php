<?php
header("Access-Control-Allow-Origin: *");
    //open connection to mysql db
    require_once 'config.php';
    //fetch table rows from mysql db
    $sql = "SELECT * FROM tblorderStatus WHERE ordstatusid <= 4";
    $result = mysqli_query($con, $sql) or die("Error in Selecting " . mysqli_error($connection));

    //create an array
    $tblorderStatusarray = array();
    while($row =mysqli_fetch_assoc($result))
    {
        $tblorderStatusarray[] = $row;
    }
    echo json_encode($tblorderStatusarray);
?>