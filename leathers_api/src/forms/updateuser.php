<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

require_once 'config.php';

$folderPath = "/var/www/html/leathers/assets/Profile/";
$imageUrlBase = "https://leathers.tamucommerce.in/api/assets/Profile/";

// Ensure folder exists
if (!file_exists($folderPath)) {
    if (!mkdir($folderPath, 0755, true)) {
        error_log("Failed to create directory: " . $folderPath);
        echo json_encode(["message" => "Failed to create image storage directory", "status" => "error"]);
        return;
    }
}

$postdata = file_get_contents("php://input");
$request = json_decode($postdata, true);

$response = [];

// Sanitize and extract fields
$name = isset($request['name']) ? mysqli_real_escape_string($con, $request['name']) : null;
$userphone = isset($request['phone']) ? mysqli_real_escape_string($con, $request['phone']) : null;
$userid = isset($request['id']) ? (int) $request['id'] : null;
$email = isset($request['email']) ? mysqli_real_escape_string($con, $request['email']) : null;
$password = isset($request['password']) ? $request['password'] : null;
$image = isset($request['image']) ? $request['image'] : null;

$image_url = null;
$image_saved = true;

// Image Save Block
if ($image && strpos($image, 'base64') !== false && strpos($image, ',') !== false) {
    list($meta, $image_data) = explode(',', $image);
    
    // Extract extension properly
    if (preg_match('/^data:image\/(\w+);base64$/', $meta, $matches)) {
        $ext = $matches[1];
    } else {
        $ext = 'png'; // fallback
    }

    $image_name = preg_replace('/\s+/', '_', $name);
    $file = $folderPath . $image_name . '.' . $ext;
    $image_url = $imageUrlBase . $image_name . '.' . $ext;

    $image_base64 = base64_decode($image_data);

    if (file_put_contents($file, $image_base64) === false) {
        error_log("Failed to save image file: " . $file);
        $image_saved = false;
    }
}

// Prepare SQL
if ($email && $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE tblusers SET userPassword = '$hash' WHERE userEmail = '$email'";
} else {
    $sql = "UPDATE tblusers SET ";
    $updates = [];

    if ($name) $updates[] = "userName = '$name'";
    if ($userphone) $updates[] = "userPhoneno = '$userphone'";
    if ($image_url && $image_saved) $updates[] = "userImage = '$image_url'";

    $sql .= implode(", ", $updates) . " WHERE userId = $userid";
}

// Execute query
if (mysqli_query($con, $sql)) {
    $response['message'] = "User details updated successfully";
    $response['status'] = $image_saved ? "success" : "warning";
    if (!$image_saved) {
        $response['note'] = "User updated, but image was not saved.";
    }
} else {
    $response['message'] = "Database update failed: " . mysqli_error($con);
    $response['status'] = "error";
}

echo json_encode($response);
