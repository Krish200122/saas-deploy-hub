<?php
 
    // header("Access-Control-Allow-Origin: *");

    // $url = $_SERVER['REQUEST_URI'];

    // $url_components = parse_url($url);
    
    // // Use parse_str() function to parse the
    // // string passed via URL
    // parse_str($url_components['query'], $params);
         
    // // Display result
    // $userId = $params['userId'];
    
    // //open connection to mysql db
    // $con = mysqli_connect("localhost","sqladmin1","P@ssw0rd12345","Ecommerce_Leathers") or die("Error " . mysqli_error($connection));
    // echo $userId;
    // //fetch table rows from mysql db
    // $sql = "SELECT ordId,ordDateTime,ordTotal,ordDiscount,ordCgst,ordSgst,netPrice,usrName,usrEmail,uadAddress,uadCountry,uadState,uadDistrict,uadPincode,uadPhoneno,orddetQty,orddetProduct FROM `tblorders` LEFT JOIN tblusers On ordCustomer = userId LEFT JOIN tblusersAddresses On uadId = ordaddId LEFT JOIN tblorderDetails On orddetOrder = ordId LEFT JOIN tblproducts On orddetProduct=prdId WHERE ordStatus = 4 and ordCustomer = $userId ORDER BY ordDateTime ASC";
    // $result = mysqli_query($con, $sql) or die("Error in Selecting " . mysqli_error($connection));

    // //create an array
    // $invoicearray = array();
    // while($row =mysqli_fetch_assoc($result))
    // {
    //     $invoicearray[] = $row;
    // }
    // echo json_encode($invoicearray);
    
    
    

// header("Access-Control-Allow-Origin: *");

// // Check if the userId parameter is set
// if (isset($_GET['userId'])) {
//     $userId = $_GET['userId'];
     
//     // Open connection to the MySQL database
//     $con = mysqli_connect("localhost", "sqladmin1", "P@ssw0rd12345", "Ecommerce_Leathers") or die("Error " . mysqli_error($connection));

//     // Sanitize the userId to prevent SQL injection
//     $userId = mysqli_real_escape_string($con, $userId);
    

//     // // Construct the SQL query
//     $sql = "SELECT ordId,ordCustomer,ordDateTime, ordTotal, ordDiscount, ordCgst, ordSgst, netPrice,userId,userName, userEmail, uadAddress, uadCountry, uadState, uadDistrict, uadPincode, uadPhoneno, orddetQty, orddetProduct,prdName,prdPrice FROM `tblorders`
//         LEFT JOIN tblusers ON userId= $userId
//         LEFT JOIN tblusersAddresses ON useraddId =$userId
//         LEFT JOIN tblorderDetails ON orddetOrder = ordId
//         LEFT JOIN tblproducts ON prdId=orddetProduct
//         WHERE ordStatus = 6 AND ordCustomer = '$userId'
//         ORDER BY ordDateTime ASC";

 

//     // Execute the query
//     $result = mysqli_query($con, $sql) or die("Error in Selecting " . mysqli_error($connection));

//     // Create an array to store the result
//     $categoriesarray = array();

//     // Fetch rows from the result set
//     while ($row = mysqli_fetch_assoc($result)) {
//         $categoriesarray[] = $row;
//     }

//     // Convert the result array to JSON and output it
//     echo json_encode($categoriesarray);
// } else {
//     echo "No userId parameter provided.";
// }




header("Access-Control-Allow-Origin: *");

    $url = $_SERVER['REQUEST_URI'];

    $url_components = parse_url($url);
    
    // Use parse_str() function to parse the
    // string passed via URL
    parse_str($url_components['query'], $params);
         
    // Display result
    $userId = $params['userId'];
    
if (isset($userId)) {

    require_once 'config.php';
    // Sanitize the userId to prevent SQL injection
    $userId = mysqli_real_escape_string($con, $userId);


    // Construct the SQL query
    $sql = "SELECT ordId, ordCustomer, ordDateTime, ordDiscount, ordCgst, ordSgst, netPrice, userId, userName, userEmail, uadAddress, uadCountry, uadState, uadDistrict, uadPincode, uadPhoneno, orddetQty, orddetProduct, prdName, prdPrice
            FROM `tblorders`
            LEFT JOIN tblusers ON userId = ordCustomer
            LEFT JOIN tblusersAddresses ON useraddId = userId
            LEFT JOIN tblorderDetails ON orddetOrder = ordId
            LEFT JOIN tblproducts ON prdId = orddetProduct
            WHERE ordStatus = 6 AND ordCustomer = '$userId'
            ORDER BY ordId, ordDateTime ASC";

    // Execute the query
    $result = mysqli_query($con, $sql) or die("Error in Selecting " . mysqli_error($connection));

    // Create an array to store the result
    $ordersArray = array();

    // Variables to track the current order
    $currentOrderId = null;
    $currentOrdDateTime = null;
    $currentOrdTotal = 0;
    $currentProductsArray = array();

    // Fetch rows from the result set
    while ($row = mysqli_fetch_assoc($result)) {
        $orderId = $row['ordId'];
        $ordDateTime = $row['ordDateTime'];

        // Check if it's a new order
        if ($orderId !== $currentOrderId || $ordDateTime !== $currentOrdDateTime) {
            // Add the previous order to the result array
            if ($currentOrderId !== null) {
                $orderObject = array(
                    'ordId' => $currentOrderId,
                    'ordDateTime' => $currentOrdDateTime,
                    'ordTotal' => $currentOrdTotal,
                    'ordDiscount' => $currentOrdDiscount,
                    'ordCgst' => $currentOrdCgst,
                    'ordSgst' => $currentOrdSgst,
                    'netPrice' => $currentNetPrice,
                    'userId' => $currentUserId,
                    'userName' => $currentUserName,
                    'userEmail' => $currentUserEmail,
                    'uadAddress' => $currentUadAddress,
                    'uadCountry' => $currentUadCountry,
                    'uadState' => $currentUadState,
                    'uadDistrict' => $currentUadDistrict,
                    'uadPincode' => $currentUadPincode,
                    'uadPhoneno' => $currentUadPhoneno,
                    'products' => $currentProductsArray
                );
                $ordersArray[] = $orderObject;
            }

            // Update the current order variables
            $currentOrderId = $orderId;
            $currentOrdDateTime = $ordDateTime;
            $currentOrdTotal = 0;
            $currentOrdDiscount = $row['ordDiscount'];
            $currentOrdCgst = $row['ordCgst'];
            $currentOrdSgst = $row['ordSgst'];
            $currentNetPrice = $row['netPrice'];
            $currentUserId = $row['userId'];
            $currentUserName = $row['userName'];
            $currentUserEmail = $row['userEmail'];
            $currentUadAddress = $row['uadAddress'];
            $currentUadCountry = $row['uadCountry'];
            $currentUadState = $row['uadState'];
            $currentUadDistrict = $row['uadDistrict'];
            $currentUadPincode = $row['uadPincode'];
            $currentUadPhoneno = $row['uadPhoneno'];
            $currentProductsArray = array();
        }

        // Calculate the ordTotal for the current order item
        $ordTotal = $row['orddetQty'] * $row['prdPrice'];

        // Add the ordTotal to the current order
        $currentOrdTotal += $ordTotal;

        // Add the product details to the current products array
        $productDetails = array(
            'orddetQty' => $row['orddetQty'],
            'orddetProduct' => $row['orddetProduct'],
            'prdName' => $row['prdName'],
            'prdPrice' => $row['prdPrice']
        );
        $currentProductsArray[] = $productDetails;
    }

    // Add the last order to the result array
    if ($currentOrderId !== null) {
        $orderObject = array(
            'ordId' => $currentOrderId,
            'ordDateTime' => $currentOrdDateTime,
            'ordTotal' => $currentOrdTotal,
            'ordDiscount' => $currentOrdDiscount,
            'ordCgst' => $currentOrdCgst,
            'ordSgst' => $currentOrdSgst,
            'netPrice' => $currentNetPrice,
            'userId' => $currentUserId,
            'userName' => $currentUserName,
            'userEmail' => $currentUserEmail,
            'uadAddress' => $currentUadAddress,
            'uadCountry' => $currentUadCountry,
            'uadState' => $currentUadState,
            'uadDistrict' => $currentUadDistrict,
            'uadPincode' => $currentUadPincode,
            'uadPhoneno' => $currentUadPhoneno,
            'products' => $currentProductsArray
        );
        $ordersArray[] = $orderObject;
    }

    // Convert the result array to JSON and output it
    echo json_encode($ordersArray);
} else {
    echo "No userId parameter provided.";
}


?>