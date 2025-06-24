<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

    $url = $_SERVER['REQUEST_URI'];

    $url_components = parse_url($url);
    
    // Use parse_str() function to parse the
    // string passed via URL
    parse_str($url_components['query'], $params);
         
    // Display result
    $userId = $params['userId'];

    require_once 'config.php';
if (mysqli_connect_errno()) {
    die('Failed to connect to MySQL: ' . mysqli_connect_error());
}

$msg = "";


$sql = "SELECT COUNT(*) AS ordState FROM tblorders WHERE ordCustomer = $userId AND ordState = 4";

$result = $con->query($sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $ordStatus = $row['ordState'];

    if ($ordStatus % 5 == 0) {
        // Generate a unique coupon code
        $couponCode = uniqid();
      
        // Determine the coupon type
        $couponType = 'percentage'; // Default type
        $discountValue = 10; // Default discount value

        // Example logic for determining coupon type based on ordStatus
        if ($ordStatus % 2 == 0) {
            $couponType = 'freeshipping';
        } else {
            $couponType = 'fixedamount';
            $discountValue = 20; // Example discount value for fixed amount coupon
        }
        

        // Set other coupon details
        $expiryDate = date('Y-m-d', strtotime('+30 days')); // Example expiry date
        $minimumOrderValue = 50; // Example minimum order value
        $usageLimit = 1; // Example usage limit
        $usageCount = 0; // Example initial usage count
        $timestamp = date('Y-m-d H:i:s');

        // Prepare the insert statement
        $stmt = $con->prepare("INSERT INTO tblcoupans (userId,coupanCode, discountType, discountValue, expirationDate, minimumorderValue, usageLimit, usageCount, createdAt, updatedAt)
                               VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?)");

         //echo $ordCustomer;
        // Bind the parameters to the statement
        $stmt->bind_param(
            'issisiiiss',
            $userId,
            $couponCode,
            $couponType,
            $discountValue,
            $expiryDate,
            $minimumOrderValue,
            $usageLimit,
            $usageCount,
            $timestamp,
            $timestamp
        );

        // Execute the statement
        if ($stmt->execute()) {
            $msg= "Coupon code: " . $couponCode;
        } else {
            $msg= "Failed to insert coupon.";
        }

        $stmt->close();
    } else {
        $msg= "No coupon generated.";
    }
} else {
    $msg= "No results found.";
}
echo json_encode($msg);
mysqli_close($con);
?>
