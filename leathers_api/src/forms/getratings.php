<?php
header("Access-Control-Allow-Origin: *");

require_once 'config.php';
$sql = "SELECT prdId,usrId, rating FROM tblrating";

$result = mysqli_query($con, $sql) or die("Error in select products." . mysqli_error($con));

// Create an array
$productsArray = array();

// Create a temporary array to store ratings for each product
$tempArray = array();

while ($row = mysqli_fetch_assoc($result)) {
    $prdId = $row['prdId'];
    $rating = $row['rating'];

    // Check if prdId exists in the temporary array
    if (isset($tempArray[$prdId])) {
        // Increment the count and add the rating
        $tempArray[$prdId]['count']++;
        $tempArray[$prdId]['totalRating'] += $rating;
    } else {
        // Initialize the count and totalRating for the prdId
        $tempArray[$prdId] = array(
            'count' => 1,
            'totalRating' => $rating
        );
    }
}

// Calculate average rating and add it to the products array
foreach ($tempArray as $prdId => $data) {
     $averageRating = round($data['totalRating'] / $data['count'], 1);
    $productsArray[] = array(
        'prdId' => $prdId,
        'averageRating' => $averageRating,
        'count' => $data['count']
    );
}

echo json_encode($productsArray);
?>
