<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    
    //open connection to mysql db
    require_once 'config.php';
    //fetch table rows from mysql db
    //$sql = "SELECT * FROM `tblusers`";
    $sql = "SELECT userName, userEmail,userPhoneno FROM `tblusers` WHERE `uid` = 'usergId';";
    
    $result = mysqli_query($con, $sql) or die("Error in Selecting " . mysqli_error($connection));

    //create an array
    $tblusersarray = array();
    while($row =mysqli_fetch_assoc($result))
    {
        $tblusersarray[] = $row;
    }
    echo json_encode($tblusersarray);
?>