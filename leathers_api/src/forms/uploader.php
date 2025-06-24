<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

$folderPath = "../assets/images/";
$destination = "../assets/thumbnails/";
$postdata = file_get_contents("php://input");
if (!empty($postdata)) {
    $request = json_decode($postdata);
    $image_parts = explode(";base64,", $request->image);
    $productId = $request->{'productId'};
    $productPrice = $request->{'productPrice'};
    $productCategory = $request->{'productCategory'};
    $productDescription = $request->{'productDescription'};
    $prdStock = $request->{'prdStock'};
    $productName = $request->{'productName'};
    $image_name = $request->{'productName'};
    $image_name = preg_replace('/\s+/', '_', $image_name);
    $image_type_aux = explode("image/", $image_parts[0]);
    $image_base64 = base64_decode($image_parts[1]);
    $file = $folderPath . $image_name . '.jpg';
    $fileName = $image_name . '.jpg';
    $filePath = $destination . $image_name . '.jpg';
    
    function createThumbnail($sourceFile, $destinationFile, $maxWidth = 500, $maxHeight = 500)
    {
        list($width, $height, $type) = getimagesize($sourceFile);
    
        // Calculate the thumbnail size while maintaining the aspect ratio
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = $width * $ratio;
        $newHeight = $height * $ratio;
    
        // Create a new image resource with the thumbnail dimensions
        $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
    
        // Load the source image
        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($sourceFile);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($sourceFile);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($sourceFile);
                break;
            default:
                return false;
        }
    
        // Resize the source image to the thumbnail size
        imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
        // Save the thumbnail image
        imagejpeg($thumbnail, $destinationFile);
    
        // Free up memory
        imagedestroy($source);
        imagedestroy($thumbnail);
    }
    
    if (file_put_contents($file, $image_base64)) {
        
        createThumbnail($file, $destination . "thump_" . $fileName);

        require_once 'config.php';
        if (mysqli_connect_errno()) {
            die('Failed to connect to MySQL: ' . mysqli_connect_error());
        }

        if ($productId == 0) {

            $query = "SELECT ifNull(Max(prdId),0) + 1 As MaxprdId from tblproducts";

            $res = mysqli_query($con, $query);
            $data = mysqli_fetch_array($res);
            $prd_Id = $data['MaxprdId'];

            $sql = "INSERT INTO tblproducts(prdId,prdName,prdPrice,prdStock,prdCategory,prdDescription,prdImage,prdMrp,prdDiscount,prdcgst,prdsgst) VALUES($prd_Id,'$productName',$productPrice,'$prdStock','$productCategory','$productDescription','https://tamucommerce.in/assets/images/$image_name.jpg',0,0,0,0)";

            if ($con->query($sql) == TRUE) {

                $response[] = array('sts' => true, 'msg' => 'Successfully uploaded');

            } else {

                $response[] = array('sts' => false, 'msg' => 'Please upload Image error in upload');

            }

        } elseif ($productId > 0) {

            $sql = "UPDATE tblproducts SET prdName = '$productName', prdPrice = $productPrice,prdStock = '$prdStock' ,prdDescription = '$productDescription',  prdImage = 'https://tamucommerce.in/assets/images/$image_name.jpg' WHERE prdId=" . $productId;

            if ($con->query($sql) == TRUE) {

                $response[] = array('sts' => true, 'msg' => 'Successfully uploaded');

            } else {

                $response[] = array('sts' => false, 'msg' => 'Please upload Image');

            }
        } else {

            $sql = "UPDATE tblproducts SET prdName = '$productName', prdPrice = $productPrice,prdStock = '$prdStock',prdDescription = '$productDescription' WHERE prdId=" . $productId;
            $con = mysqli_connect("localhost", "sqladmin1", "P@ssw0rd12345", "Ecommerce_Leathers") or die("Error in connection." . mysqli_error($con));
            if ($con->query($sql) == TRUE) {

                $response[] = array('sts' => true, 'msg' => 'Product updated');

            } else {

                $response[] = array('sts' => false, 'msg' => 'Please upload Image error');

            }
            //$response[] = array('sts'=>false,'msg'=>'Please upload Image');

        }

    } else {

        $sql = "UPDATE tblproducts SET prdName = '$productName', prdPrice = $productPrice ,prdStock = '$prdStock' ,prdDescription = '$productDescription' WHERE prdId=" . $productId;
        $con = mysqli_connect("localhost","sqladmin1","P@ssw0rd12345","Ecommerce_Leathers") or die("Error " . mysqli_error($con));
        if ($con->query($sql) == TRUE) {

            $response[] = array('sts' => true, 'msg' => 'Product updated');

        } else {

            $response[] = array('sts' => false, 'msg' => 'Please upload Image');

        }
        //$response[] = array('sts'=>false,'msg'=>'Please upload Image');

    }
    echo json_encode($response);
}
?>