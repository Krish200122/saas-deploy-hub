<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$jsonValue = file_get_contents('php://input');
$decodedValue = json_decode($jsonValue, true);

$response = array();

if (!empty($decodedValue)) {
    require_once 'config.php';

    $folderPath = "/var/www/html/leathers/assets/Banners/";

    // Sanitize inputs
    $banId = isset($decodedValue['bnrId']) ? intval($decodedValue['bnrId']) : 0;
    $description = isset($decodedValue['description']) ? mysqli_real_escape_string($con, $decodedValue['description']) : null;
    $status = isset($decodedValue['status']) ? mysqli_real_escape_string($con, $decodedValue['status']) : null;
    $title = isset($decodedValue['title']) ? mysqli_real_escape_string($con, $decodedValue['title']) : null;

    $imageData = isset($decodedValue['image']) ? $decodedValue['image'] : '';
    $img = ''; // final image URL

    // Check if image is base64 (indicates new image upload)
    if (strpos($imageData, 'base64') !== false) {
        if (!is_dir($folderPath)) {
            if (!mkdir($folderPath, 0777, true)) {
                echo json_encode(["message" => "Failed to create directory", "status" => "error"]);
                exit;
            }
        }

        $image_parts = explode(";base64,", $imageData);
        if (count($image_parts) < 2) {
            echo json_encode(["message" => "Invalid image format", "status" => "error"]);
            exit;
        }

        $image_base64 = base64_decode($image_parts[1]);
        $image_name = preg_replace('/\s+/', '_', $title);
        $file = $folderPath . $image_name . '.jpg';

        if (file_put_contents($file, $image_base64) === false) {
            echo json_encode(["message" => "Failed to save image file", "status" => "error"]);
            exit;
        }

        $img = "http://88.222.244.141:98/leathers/assets/Banners/$image_name.jpg";
    } else {
        // Use the existing image URL (when not base64)
        $img = $imageData;
    }

    // Perform DB operations
    if ($banId == 0) {
        $query = "SELECT IFNULL(MAX(bnrId), 0) + 1 AS MaxbnrId FROM tblbanners";
        $res = mysqli_query($con, $query);
        $data = mysqli_fetch_array($res);
        $ban_Id = $data['MaxbnrId'];

        $checkQuery = "SELECT * FROM tblbanners WHERE bnrTitle = '$title' AND bnrDescription = '$description'";
        $checkResult = mysqli_query($con, $checkQuery);

        if (mysqli_num_rows($checkResult) > 0) {
            echo json_encode(["message" => "A banner with the same title and description already exists.", "status" => "error"]);
            exit;
        }

        $sql = "INSERT INTO tblbanners (bnrId, bnrTitle, bnrDescription, status, bnrImage) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "issss", $ban_Id, $title, $description, $status, $img);

        if (mysqli_stmt_execute($stmt)) {
            $response = ["message" => "Banner added successfully", "status" => "success"];
        } else {
            $response = ["message" => "Error adding banner", "status" => "error"];
        }
        mysqli_stmt_close($stmt);
    } else {
        // Update existing banner
        $sql = "UPDATE tblbanners SET bnrTitle = ?, bnrDescription = ?, status = ?, bnrImage = ? WHERE bnrId = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ssssi", $title, $description, $status, $img, $banId);

        if (mysqli_stmt_execute($stmt)) {
            $response = ["message" => "Banner updated successfully", "status" => "success"];
        } else {
            $response = ["message" => "Error updating banner", "status" => "error"];
        }
        mysqli_stmt_close($stmt);
    }

    mysqli_close($con);
} else {
    $response = ["message" => "No data received", "status" => "error"];
}

echo json_encode($response);
?>
