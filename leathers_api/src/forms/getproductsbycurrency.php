<?php
header("Access-Control-Allow-Origin: *");

$url = $_SERVER['REQUEST_URI'];

$url_components = parse_url($url);

// Use parse_str() function to parse the
// string passed via URL
parse_str($url_components['query'], $params);
$currency = $params['currency'];

// Set default value for $order if not provided
//$order = isset($params['order']) ? $params['order'] : 'ASC';

//open connection to mysql db
require_once 'config.php';
//mysqli_select_db("Ecommerce_Aquatics",$con);
//fetch table rows from mysql db

$sql = "SELECT prdId, prdName, prdCategory, prdImage, prdThampnail, prdPrice, prdDescription, catName FROM `tblproducts` LEFT JOIN `tblcategories` ON prdCategory = catId  ORDER BY prdPrice DESC";

$result = mysqli_query($con, $sql) or die("Error in select products." . mysqli_error($connection));

//create an array
$productsarray = array();

while ($row = mysqli_fetch_assoc($result)) {
    // Convert the price to USD if the currency is USD
    if ($currency === 'USD') {
        $price_in_inr = floatval($row['prdPrice']);
        $exchange_rate = 0.012;
        $price_in_usd = $price_in_inr * $exchange_rate;
        $row['prdPrice'] = number_format($price_in_usd, 2); // Format to two decimal places
    } elseif ($currency === 'EUR') {
        $price_in_inr = floatval($row['prdPrice']);
        $exchange_rate = 0.011;
        $price_in_eur = $price_in_inr * $exchange_rate;
        $row['prdPrice'] = number_format($price_in_eur, 2); // Format to two decimal places
    }

    $productsarray[] = $row;
}

echo json_encode($productsarray);
?>
