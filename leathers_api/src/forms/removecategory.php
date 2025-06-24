<?php
header("Access-Control-Allow-Origin: *");

$url = $_SERVER['REQUEST_URI'];

$url_components = parse_url($url);

// Use parse_str() function to parse the
// string passed via URL
parse_str($url_components['query'], $params);

// Display result
$CatId = $params['catId'];

// Open connection to mysql db
require_once 'config.php';

try {
    // Delete subcategories first
    $sqlSub = "DELETE FROM `tblsubcategories` WHERE subcatCat = " . $CatId;
    $con->query($sqlSub);

    // Delete the category
    $sqlCat = "DELETE FROM `tblcategories` WHERE catId = " . $CatId;
    $con->query($sqlCat);

    // Commit the transaction
    mysqli_commit($con);

    $response[] = array('sts' => true, 'msg' => 'Successfully removed');
} catch (Exception $e) {
    // Rollback the transaction in case of error
    mysqli_rollback($con);
    $response[] = array('sts' => false, 'msg' => 'Error in removing item');
}

// Close the connection
mysqli_close($con);

echo json_encode($response);
?>
