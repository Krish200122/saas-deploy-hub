<?php
header("Access-Control-Allow-Origin: *");


// Open connection to MySQL db
require_once 'config.php';
$sql = "SELECT * FROM `tblsplOffers1` WHERE STR_TO_DATE(`spldate`, '%d/%m/%Y') >= STR_TO_DATE('$currentDateFormatted', '%d/%m/%Y')";

$result = mysqli_query($con, $sql) or die("Error in select products." . mysqli_error($con));

// Create an array
$productsarray = array();

while ($row = mysqli_fetch_assoc($result)) {
    $productsarray[] = $row;
}

echo json_encode($productsarray);

mysqli_close($con);
?>
