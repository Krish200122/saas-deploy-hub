<?php

// Database connection settings

$host = "88.222.244.141";

$dbname = "Ecommerce_Leathers";

$username = "root";  // Change if using another MySQL user

$password = "TAMU@2024";
 
// Establish MySQLi connection

$con = mysqli_connect($host, $username, $password, $dbname);
 
// Check connection

if (!$con) {

    die("Database connection failed: " . mysqli_connect_error());

}
 
// Optional: Set character encoding to UTF-8

mysqli_set_charset($con, "utf8");

?>
 