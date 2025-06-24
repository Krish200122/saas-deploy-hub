<?php

header("Access-Control-Allow-Origin: *");

$url = $_SERVER['REQUEST_URI'];
$url_components = parse_url($url);


parse_str($url_components['query'], $params);


$admin = $params['admin'];


require_once 'config.php';

if ($admin == 1) {
    $sql = "SELECT * FROM `tblbanners`";
} else {
    $sql = "SELECT * FROM `tblbanners` WHERE status = 'true'";
}
$result = mysqli_query($con, $sql) or die("Error in select banners: " . mysqli_error($con));


$bannersarray = array();

while ($row = mysqli_fetch_assoc($result)) {
    $bannersarray[] = $row;
}

// Encode the array as JSON and send it as the response
echo json_encode($bannersarray);

?>
