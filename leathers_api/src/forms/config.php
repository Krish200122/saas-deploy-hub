<?php

// Database connection settings from environment variables
$host     = getenv('DB_HOST');
$dbname   = getenv('DB_NAME');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');





// Establish MySQLi connection
$con = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$con) {
    die("❌ Database connection failed: " . mysqli_connect_error());
}

// Optional: Set character encoding to UTF-8
mysqli_set_charset($con, "utf8");

?>
