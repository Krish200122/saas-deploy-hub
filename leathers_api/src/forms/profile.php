<?php
    header("Access-Control-Allow-Origin: *");
    
    $jsonUser = file_get_contents('php://input');
    $jsonUser = html_entity_decode(  $jsonUser);
    $User = json_decode(  $jsonUser);
    
    //open connection to mysql db
    require_once 'config.php';
    //fetch table rows from mysql db
    $sql = "SELECT * FROM tblusersAddresses where uadId=".$User -> {'currentuser'}."";
    $result = mysqli_query($con, $sql) or die("Error in Selecting " . mysqli_error($connection));
    echo "userId = ".$User->{'currentuser'}.
    //create an array
    $tblusersarray = array();
    while($row =mysqli_fetch_assoc($result))
    {
        $tblusersarray[] = $row;
    }
    echo json_encode($tblusersarray);
?>