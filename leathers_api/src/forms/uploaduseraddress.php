<?php
 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
// Retrieve and decode JSON input
$jsonAddress = file_get_contents('php://input');
$Address = json_decode(html_entity_decode($jsonAddress));
 
// Validate input
if (!$Address) {
   echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
   exit;
}
 
// Extract data
$uadId = $Address->id ?? null;
$userName = $Address->userName;
$phoneno = $Address->phoneno;
$landMark = $Address->landMark;
$pinCode = $Address->pinCode;
$address = $Address->address;
$city = $Address->city;
$state = $Address->state;
$country = $Address->country;
$type = $Address->adtype;
$userId = $Address->userId;
$isDefaultAddress = $Address->isDefaultAddress;
 
// Database connection
require_once 'config.php';
 
// Handle default address flag
if ($isDefaultAddress=='1') {
   
    $updateDefault = $con->prepare("UPDATE tblusersAddresses SET uadDefaultAddress = 0 WHERE uadDefaultAddress=1 AND useraddId= ?");
    $updateDefault->bind_param("i", $userId);
    $updateDefault->execute();
    $updateDefault->close();
}
$addaddress= "select * from tblusersAddresses where uadDefaultAddress=1 AND useraddId= '$userId'";
$addaddress1= mysqli_query($con, $addaddress);
 
if (mysqli_num_rows($addaddress1) == 0) {
   
 $isDefaultAddress=1;  
}
 
// Check for duplicate address
$duplicateSql = "
SELECT COUNT(*) as count FROM tblusersAddresses
WHERE useraddId = ? AND uadUserName = ? AND uadType = ? AND uadLandmark = ? AND
      uadAddress = ? AND uadCountry = ? AND uadState = ? AND uadDistrict = ? AND
      uadPincode = ? AND uadPhoneno = ?" .
      ($uadId !== null ? " AND uadId != ?" : "");
 
$duplicateStmt = $con->prepare($duplicateSql);
 
if ($uadId !== null) {
$duplicateStmt->bind_param("isssssssssi", $userId, $userName, $type, $landMark, $address,
                                          $country, $state, $city, $pinCode, $phoneno, $uadId);
} else {
$duplicateStmt->bind_param("isssssssss", $userId, $userName, $type, $landMark, $address,
                                         $country, $state, $city, $pinCode, $phoneno);
}
 
$duplicateStmt->execute();
$duplicateResult = $duplicateStmt->get_result();
$duplicateRow = $duplicateResult->fetch_assoc();
$duplicateStmt->close();
 
if ($duplicateRow['count'] > 0) {
echo json_encode(['status' => 'error', 'message' => 'This address already exists for the user.']);
$con->close();
exit;
}
 
if ($uadId !== null) {
    // Update existing address
    $stmt = $con->prepare("
        UPDATE tblusersAddresses
        SET uadUserName = ?, uadType = ?, uadLandmark = ?, uadAddress = ?, uadCountry = ?,
            uadState = ?, uadDistrict = ?, uadPincode = ?, uadPhoneno = ?, uadDefaultAddress = ?
        WHERE uadId = ?
    ");
    $stmt->bind_param("ssssssssiii", $userName, $type, $landMark, $address, $country,
                                 $state, $city, $pinCode, $phoneno, $isDefaultAddress, $uadId);
} else {
    // Insert new address
    $res = $con->query("SELECT IFNULL(MAX(uadId), 0) + 1 AS MaxuadId FROM tblusersAddresses");
    $data = $res->fetch_assoc();
    $Sno = $data['MaxuadId'];
 
    $stmt = $con->prepare("
        INSERT INTO tblusersAddresses(uadId, useraddId, uadUserName, uadType, uadLandmark, uadAddress,
                                      uadCountry, uadState, uadDistrict, uadPincode, uadPhoneno, uadDefaultAddress)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iissssssssii", $Sno, $userId, $userName, $type, $landMark, $address,
                                  $country, $state, $city, $pinCode, $phoneno, $isDefaultAddress);
}
 
// Execute query and handle response
if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Address updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => "Error: " . $stmt->error]);
}
 
$stmt->close();
$con->close();
 
?>