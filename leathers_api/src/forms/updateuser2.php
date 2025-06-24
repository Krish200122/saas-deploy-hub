<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

require_once 'config.php'; // Database connection

$folderPath = "../assets/Profile/";
$postdata = file_get_contents("php://input");
$request = json_decode($postdata, true);

// Extract user inputs
$name = $request['name'] ?? '';
$lastname = $request['lastname'] ?? '';
$userphone = $request['phone'] ?? '';
$userid = $request['id'] ?? '';
$email = $request['email'] ?? '';
$password = $request['password'] ?? '';

$hash = $password ? password_hash($password, PASSWORD_DEFAULT) : null;

// Handle image upload
$userImage = null;
if (!empty($request['image'])) {
    $image_parts = explode(";base64,", $request['image']);
    
    if (count($image_parts) == 2) {
        $image_base64 = base64_decode($image_parts[1]);
        $image_extension = explode('/', mime_content_type($image_parts[0]))[1] ?? 'jpg';
        $image_name = preg_replace('/\s+/', '_', $name) . '.' . $image_extension;
        $file = $folderPath . $image_name;

        if (!file_put_contents($file, $image_base64)) {
            die(json_encode(["message" => "Failed to save image", "status" => "error"]));
        }
        $userImage = "http://88.222.244.141:98/leathers/assets/Profile/" . $image_name;
    }
}

// Build SQL query dynamically
$updateFields = [];
$params = [];
$types = "";

if ($name) {
    $updateFields[] = "userName = ?";
    $params[] = $name;
    $types .= "s";
}
if ($lastname) {
    $updateFields[] = "userLastName = ?";
    $params[] = $lastname;
    $types .= "s";
}
if ($userphone) {
    $updateFields[] = "userPhoneno = ?";
    $params[] = $userphone;
    $types .= "s";
}
if ($userImage) {
    $updateFields[] = "userImage = ?";
    $params[] = $userImage;
    $types .= "s";
}
if ($email && $password) {
    $updateFields[] = "userPassword = ?";
    $params[] = $hash;
    $types .= "s";
}

if (!empty($updateFields)) {
    $sql = "UPDATE tblusers SET " . implode(", ", $updateFields) . " WHERE userId = ?";
    $params[] = $userid;
    $types .= "i";

    $stmt = $con->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo json_encode(["message" => "User details updated successfully", "status" => "success"]);
    } else {
        echo json_encode(["message" => "Database error: " . $stmt->error, "status" => "error"]);
    }
} else {
    echo json_encode(["message" => "No fields to update", "status" => "error"]);
}
?>
