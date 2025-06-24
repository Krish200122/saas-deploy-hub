<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$jsonValue = file_get_contents('php://input');
$jsonValue = html_entity_decode($jsonValue);
$Value = json_decode($jsonValue, true);

$speId = isset($Value['specId']) ? $Value['specId'] : null;
$sprdId = $Value['prdId'];
$spe1 = $Value['spec1'];
$spe2 = $Value['spec2'];
$spe3 = $Value['spec3'];
$spe4 = $Value['spec4'];
$spe5 = $Value['spec5'];
$spe6 = $Value['spec6'];
$spe7 = $Value['spec7'];
$valu1 = $Value['value1'];
$valu2 = $Value['value2'];
$valu3 = $Value['value3'];
$valu4 = $Value['value4'];
$valu5 = $Value['value5'];
$valu6 = $Value['value6'];
$valu7 = $Value['value7'];

require_once 'config.php';

$response = array();

if ($speId !== null) {
    // UPDATE with prepared statement
    $sql = "UPDATE tblspecification SET 
        spec1 = ?, spec2 = ?, spec3 = ?, spec4 = ?, spec5 = ?, spec6 = ?, spec7 = ?, 
        value1 = ?, value2 = ?, value3 = ?, value4 = ?, value5 = ?, value6 = ?, value7 = ?
        WHERE specId = ?";
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssssssssssssssi", 
        $spe1, $spe2, $spe3, $spe4, $spe5, $spe6, $spe7,
        $valu1, $valu2, $valu3, $valu4, $valu5, $valu6, $valu7,
        $speId
    );

    if ($stmt->execute()) {
        $response['message'] = "Specification updated successfully";
        $response['status'] = "success";
    } else {
        $response['message'] = "Error updating Specification: " . $stmt->error;
        $response['status'] = "error";
    }

    $stmt->close();

} else {
    if($valu1 == 0 && $valu2 == 0 && $valu4 == 0) {
        $response['message'] = "Please Enter the value";
        $response['status'] = "error";
    } else {
        // Get max specId
        $query = "SELECT IFNULL(MAX(specId), 0) + 1 AS MaxspecId FROM tblspecification";
        $res = mysqli_query($con, $query);
        $data = mysqli_fetch_array($res);
        $MaxspecId = $data['MaxspecId'];

        // INSERT with prepared statement
        $sql = "INSERT INTO tblspecification 
            (specId, specprdId, spec1, spec2, spec3, spec4, spec5, value1, value2, value3, value4, value5) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $con->prepare($sql);
        $stmt->bind_param("iissssssssss", 
            $MaxspecId, $sprdId, $spe1, $spe2, $spe3, $spe4, $spe5, 
            $valu1, $valu2, $valu3, $valu4, $valu5
        );

        if ($stmt->execute()) {
            $response['message'] = "Specification created successfully";
            $response['status'] = "success";
        } else {
            $response['message'] = "Error creating Specification: " . $stmt->error;
            $response['status'] = "error";
        }

        $stmt->close();
    }
}

mysqli_close($con);

echo json_encode($response);
?>
