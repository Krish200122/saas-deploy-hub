<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$jsonValue = file_get_contents('php://input');
$jsonValue = html_entity_decode($jsonValue);
$Value = json_decode($jsonValue);

// Create connection
require_once 'config.php';
$splvarId = $Value->{'varId'};
$spldate = $Value->{'date'};
$sploccasion = $Value->{'occasion'};
$sploffPrice = $Value->{'offerprice'};
$splId = $Value->{'splId'};
$variantQuery = "SELECT * FROM tblvariantproduct WHERE variantId = $splvarId";
$variantResult = mysqli_query($con, $variantQuery);

if (mysqli_num_rows($variantResult) > 0) {

    $variantQuery1 = "SELECT * FROM tblsplOffers WHERE splvarId = $splvarId";
    $variantResult1 = mysqli_query($con, $variantQuery1);
    
    if (mysqli_num_rows($variantResult1) > 0) {
    
        $sql1 = "UPDATE tblsplOffers SET spldate = '$spldate', sploccasion = '$sploccasion', sploffPrice = $sploffPrice WHERE splId = $splId ";
         if (mysqli_query($con, $sql1)) {
            $response['message'] = "Special offer updated successfully";
            $response['status'] = "success";
        } else {
            $sql2 = "UPDATE tblsplOffers SET spldate = '$spldate', sploccasion = '$sploccasion', sploffPrice = $sploffPrice WHERE splvarId = $splvarId ";
         if (mysqli_query($con, $sql2)) {
            $response['message'] = "Special offer updated successfully";
            $response['status'] = "success";
        } else {
             $response['message'] = "error in updating specialoffer: " . $sql . "<br>" . mysqli_error($con);
             $response['status'] = "error";
        }

        }
        
    }else{
       
        $query = "SELECT IFNULL(MAX(splId), 0) + 1 AS MaxsplId FROM tblsplOffers";
        $res = mysqli_query($con, $query);
        $data = mysqli_fetch_array($res);
        $MaxsplId = $data['MaxsplId'];
        
        $sql = "INSERT INTO tblsplOffers(splId,splvarId,spldate,sploccasion,sploffPrice)VALUES($MaxsplId,$splvarId,'$spldate','$sploccasion',$sploffPrice)";
                
        if (mysqli_query($con, $sql)) {
            $response['message'] = "Special offer added successfully";
            $response['status'] = "success";
        } else {
            $response['message'] = "error in adding specialoffer: " . $sql . "<br>" . mysqli_error($con);
             $response['status'] = "error";
        }
    }
}else{
      $response['message'] = "Product does not exists " . $sql . "<br>" . mysqli_error($con);
      $response['status'] = "error";
 }

    
echo json_encode($response);  
mysqli_close($con);
?>
