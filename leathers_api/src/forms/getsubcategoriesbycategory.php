<?php
header("Access-Control-Allow-Origin: *");

$url = $_SERVER['REQUEST_URI'];

$url_components = parse_url($url);

// Use parse_str() function to parse the string passed via URL
parse_str($url_components['query'], $params);

// Get the 'catId' and 'admin' parameters
$catId = $params['catId'];
$admin = isset($params['admin']) ? $params['admin'] : 0; // Default to 0 if 'admin' is not provided

// Open a connection to the MySQL database
require_once 'config.php';
// Build the SQL query based on the 'admin' parameter
if ($admin == 1) {
    // If 'admin' is 1, get all rows
    $sql = "SELECT subcatId, subcatName, status FROM tblsubcategories WHERE subcatCat = $catId";
} else {
    // If 'admin' is 0 (or not provided), get only active rows
    $sql = "SELECT subcatId, subcatName, status FROM tblsubcategories WHERE subcatCat = $catId AND status = 'true'";
}

   $result = mysqli_query($con, $sql) or die("Error in Selecting " . mysqli_error($connection));

// Create an array to store the results
$categoriesarray = array();
while ($row = mysqli_fetch_assoc($result)) {
    $categoriesarray[] = $row;
}

echo json_encode($categoriesarray);
?>
