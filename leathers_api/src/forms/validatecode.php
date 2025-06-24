<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$jsonCoupan = file_get_contents('php://input');
$jsonCoupan = html_entity_decode($jsonCoupan);
$Coupan = json_decode($jsonCoupan);

$amount = $Coupan->amount;
$couponCode = $Coupan->coupancode;
$currentDate = $Coupan->date;

// Open connection to MySQL database
require_once 'config.php';
// Prepare and execute a database query to fetch the coupon data based on the coupon code
$query = "SELECT * FROM tblcoupans WHERE coupanCode = '$couponCode'";

$result = mysqli_query($con, $query) or die("Error in select products: " . mysqli_error($con));

// Check if a coupon with the entered code exists in the database
if (mysqli_num_rows($result) > 0) {
    $coupon = mysqli_fetch_assoc($result);

    $expiryDate = $coupon['expirationDate'];
    $minimumAmount = $coupon['minimumorderValue'];
    $usageLimit = $coupon['usageLimit'];
    $usageCount = $coupon['usageCount'];

    // Validate the coupon conditions
    if ($couponCode === $coupon['coupanCode'] &&
        $currentDate < $expiryDate &&
        $amount >= $minimumAmount &&
        $usageCount < $usageLimit) {

        // Check the discount type and call the corresponding function
        $discountType = $coupon['discountType'];
        $discountValue = $coupon['discountValue'];

        if ($discountType === 'percentage') {
            $discountedAmount = applyPercentageDiscount($amount, $discountValue);
        } elseif ($discountType === 'fixedamount') {
            $discountedAmount = applyFixedDiscount($amount, $discountValue);
        } elseif ($discountType === 'freeshipping') {
            $discountedAmount = applyFreeShipping($amount, $discountValue);
        } else {
            $discountedAmount = $amount;
        }

        // Calculate the discount value
        $discount = $amount - $discountedAmount;

        // Update the usage count for the coupon in the database
        $usageCount++;

        // Update the usage count in the database
        $updateQuery = "UPDATE tblcoupans SET usageCount = $usageCount WHERE coupanCode = '$couponCode'";
        mysqli_query($con, $updateQuery) or die("Error updating usage count: " . mysqli_error($con));

        $resultAmount = $discountedAmount;
        $disAmount = $discount;
        $message = "Hurray! Discount is Applied. Discounted Amount: " . $resultAmount;
    } else {
        $resultAmount = $amount;
        $message = "Coupon is already used.";
    }
} else {
    $resultAmount = $amount;
    $message = "The promotional code you entered is not valid. Original Amount: " . $amount;
}

function applyPercentageDiscount($amount, $discountValue) {
    $discountedAmount = $amount - ($amount * $discountValue / 100);
    return $discountedAmount;
}

function applyFixedDiscount($amount, $discountValue) {
    $discountedAmount = $amount - $discountValue;
    return $discountedAmount;
}

function applyFreeShipping($amount, $discountValue) {
    //$couponCode = generateCouponCode();
    //$coupon = array(
    //    'code' => $couponCode,
    //    'type' => 'free_shipping',
    //    'value' => 0,
    //);
    //return $coupon;
    $discountedAmount = $amount - 50;
    return $discountedAmount;

}

$response = [
    'resultAmount' => $resultAmount,
    'message' => $message,
    'discount' => $disAmount
];

echo json_encode($response);

mysqli_close($con);
?>
