<?php


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

$folderPath = "../assets/Banners/";
$postdata = file_get_contents("php://input");

$response = array();

if (!empty($postdata)) {
    $request = json_decode($postdata, true);
    require_once 'config.php';

    // Sanitize and validate input
    $bnrId = mysqli_real_escape_string($con, $request['bnrId']);
    $status = mysqli_real_escape_string($con, $request['status']);


   
        
        try {

                 $sql = "UPDATE tblbanners SET 
                            status = '$status'
                            WHERE bnrId = $bnrId";
                             if (mysqli_query($con, $sql)) {
                    $response['message'] = "Banner updated successfullyy";
                    $response['status'] = "success";
                } else {
                    $response['message'] = "Error: " . $sql . "<br>" . mysqli_error($con);
                    $response['status'] = "error";
                }
            
            
        } catch (Exception $e) {
            $response[] = array('sts' => false, 'msg' => 'Exception: ' . $e->getMessage());
        }
    

    mysqli_close($con);
} else {
    $response[] = array('sts' => false, 'msg' => 'No data received');
}

echo json_encode($response);
?>