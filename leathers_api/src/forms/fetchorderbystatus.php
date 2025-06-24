<?php
   header("Access-Control-Allow-Origin: *");
   
   $url = $_SERVER['REQUEST_URI'];

    $url_components = parse_url($url);
    
    // Use parse_str() function to parse the
    // string passed via URL
    parse_str($url_components['query'], $params);
         
    // Display result
    $statusId = $params['statusId'];
    //open connection to mysql db
    require_once 'config.php';
if($statusId){
    $sql = "SELECT * FROM `tblorders` LEFT JOIN tblorderStatus On ordStatus = ordstatusId LEFT JOIN tblusersAddresses On uadId = ordaddId WHERE ordStatus = $statusId  ORDER BY ordId ASC";
    $result = mysqli_query($con, $sql) or die("Error in Selecting " . mysqli_error($con));
}

    //create an array
    $categoriesarray = array();
    while($row =mysqli_fetch_assoc($result))
    {
        $categoriesarray[] = $row;
    }
    echo json_encode($categoriesarray);
?>