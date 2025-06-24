<?php
$trackingNumber = "1234567890";
 $apiKey = "eBAPtNRv2OXYJlnwS5x9Dk51Tgw11ijq";
$curl = curl_init();
curl_setopt_array($curl, [
CURLOPT_URL => "https://api-mock.dhl.com/mydhlapi/tracking?shipmentTrackingNumber={$trackingNumber}",
CURLOPT_RETURNTRANSFER => true,
CURLOPT_ENCODING =>"",
CURLOPT_MAXREDIRS => 10,
CURLOPT_TIMEOUT => 30,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => "GET",
CURLOPT_HTTPHEADER => [
     "accept: application/json",
//         // "Message-Reference: d0e7832e-5c98-11ea-bc55-0242ac13",
//         // "Message-Reference-Date: Wed, 21 Oct 2015 07:28:00 GMT",
        "Accept-Language: eng",
        "Plugin-Name: ",
        "Plugin-Version: ",
        "Shipping-System-Platform-Name: ",
        "Shipping-System-Platform-Version: ",
        "Webstore-Platform-Name: ",
        "Webstore-Platform-Version: ",
       // "DHL-API-Key: eBAPtNRv2OXYJlnwS5x9Dk51Tgw11ijq",
        "Authorization: Basic ZGVtby1rZXk6ZGVtby1zZWNyZXQ=",
],
]);
$response = curl_exec($curl);
$err= curl_error($curl);
curl_close($curl);
if ($err) {
echo "CURL Error #:" . $err;
} else {
    echo $response;
}




// $trackingNumber = "1234567890"; // Replace with your actual tracking number
// //$apiKey = "YgE5EVrQBueQKvbP2zeGQA4BaWgC8rAn"; // Replace with your DHL API key
// $apiKey = "ZGVtby1rZXk6ZGVtby1zZWNyZXQ=";


// // DHL API endpoint for shipment tracking
// $endpoint = "https://api-mock.dhl.com/mydhlapi/tracking?shipmentTrackingNumber={$trackingNumber}";

// $curl = curl_init();
// curl_setopt_array($curl, [
//     CURLOPT_URL => $endpoint,
//     CURLOPT_RETURNTRANSFER => true,
//     CURLOPT_ENCODING => "",
//     CURLOPT_MAXREDIRS => 10,
//     CURLOPT_TIMEOUT => 30,
//     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//     CURLOPT_CUSTOMREQUEST => "GET",
//     CURLOPT_HTTPHEADER => [
//         "accept: application/json",
//         // "Message-Reference: d0e7832e-5c98-11ea-bc55-0242ac13",
//         // "Message-Reference-Date: Wed, 21 Oct 2015 07:28:00 GMT",
//         "Accept-Language: eng",
//         "Plugin-Name: ",
//         "Plugin-Version: ",
//         "Shipping-System-Platform-Name: ",
//         "Shipping-System-Platform-Version: ",
//         "Webstore-Platform-Name: ",
//         "Webstore-Platform-Version: ",
//        "Authorization: Basic {$apiKey}",
//     ],
// ]);

// $response = curl_exec($curl);
// $err = curl_error($curl);

// curl_close($curl);

// if ($err) {
//     echo "cURL Error #:" . $err;
// } else {
//     // Decode the JSON response
//     $data = json_decode($response, true);

//     // Output the response
//     echo "<pre>";
//     print_r($data);
//     echo "</pre>";
// }








?>