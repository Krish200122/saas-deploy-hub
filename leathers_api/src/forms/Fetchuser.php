<?php
    header("Access-Control-Allow-Origin: *");
    
    $jsonUser = file_get_contents('php://input');
    $jsonUser = html_entity_decode($jsonUser);
    $User = json_decode($jsonUser);
    
    //open connection to mysql db
    require_once 'config.php';
    if($User ->{'email'} && 'isAdmin' == true){
        //fetch table rows from mysql db
        $sql = "SELECT userId,isAdmin FROM tblusers where userEmail='".$User->{'email'}."'";
     
    } else {
      //fetch table rows from mysql db
        $sql = "SELECT userId FROM tblusers where userEmail='".$User->{'email'}."'";

    }

    $result = mysqli_query($con, $sql) or die("Error in Selecting " . mysqli_error($con));

    //create an array
    $tblusersarray = array();
    while($row =mysqli_fetch_assoc($result))
    {
        $tblusersarray[] = $row;
    }
    echo json_encode($tblusersarray);
?>