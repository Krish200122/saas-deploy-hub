<?php
ini_set('memory_limit', '256M');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

$folderPath = "/var/www/html/leathers/assets/Images/";
$thumbnailPath = "/var/www/html/leathers/assets/thumbnails/";

// Ensure directories exist or create them
if (!file_exists($folderPath)) {
    if (!mkdir($folderPath, 0755, true)) {
        error_log("Failed to create directory: " . $folderPath);
        $response = array("message" => "Failed to create image storage directory", "status" => "error");
        echo json_encode($response);
        exit;
    }
}

if (!file_exists($thumbnailPath)) {
    if (!mkdir($thumbnailPath, 0755, true)) {
        error_log("Failed to create directory: " . $thumbnailPath);
        $response = array("message" => "Failed to create thumbnail storage directory", "status" => "error");
        echo json_encode($response);
        exit;
    }
}

$postdata = file_get_contents("php://input");

if (!empty($postdata)) {
    $request = json_decode($postdata, true); // Decode as an associative array

    // Ensure the prdName key exists in the request data
    if (!isset($request['prdName'])) {
        $response[] = array('sts' => false, 'msg' => 'Missing "prdName" key in the request data');
        echo json_encode($response);
        exit;
    }

    // Database connection
    require_once 'config.php';
    $productId       = $request['prdId']        ?? null;
    $variant_id      = $request['variantId']    ?? null;
    $productName     = $request['prdName']      ?? null;
    $catId           = $request['catId']        ?? null;
    $subCatId        = $request['SubcatId']     ?? null;
    $prdPrice        = $request['prdPrice']     ?? null;
    $taxaddedamount  = $request['taxamount']   ?? null;
    $prdDescription  = mysqli_real_escape_string($con, $request['prdDescription'] ?? '');
    $CGST            = $request['CGST']         ?? null;
    $SGST            = $request['SGST']         ?? null;
    $HSN             = $request['HSN']          ?? "NULL";
    $prdDiscount     = $request['prdDiscount']  ?? null;
    $status          = $request['status']       ?? null;
    $mrpPrice        = $request['mrpPrice']     ?? null;
    $colorId         = $request['colorId']      ?? null;
    $sizeId          = $request['sizeId']       ?? 0;
    $stock           = $request['stock']        ?? null;

    $fvo             = $request['fvo']          ?? null;
    $bvo             = $request['bvo']          ?? null;
    $tvo             = $request['tvo']          ?? null;
    $svo             = $request['svo']          ?? null;
    $avo             = $request['avo']          ?? null;

    $fvt             = $request['fvt']          ?? null;
    $bvt             = $request['bvt']          ?? null;
    $tvt             = $request['tvt']          ?? null;
    $svt             = $request['svt']          ?? null;
    $avt             = $request['avt']          ?? null;

    // Array to store the relative paths for the thumbnails and original images
    $thumbnailPaths = array();
    $originalPaths = array();
    // Custom function to generate a unique identifier
    function generateUniqueIdentifier()
    {
        $timestamp = microtime(true) * 10000; // Include microseconds for extra uniqueness
        $randomNumber = mt_rand(1000, 9999); // Generate a random number
        $customPrefix = 'zuna'; // Add a custom prefix

        return $customPrefix . $timestamp . '_' . $randomNumber;
    }

    foreach ($request['Uploaded_images'] as $imageName => $imageData) {
        $image_parts = explode(";base64,", $imageData);
        $image_base64 = base64_decode($image_parts[1]);

        // Create image resources from base64 data
        $image = imagecreatefromstring($image_base64);

        // Get the original dimensions
        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);

        // Determine the desired dimensions for the original image
        $maxOriginalWidth = 4000;
        $maxOriginalHeight = 4000;

        // Calculate new dimensions to fit within the maximum dimensions
        if ($originalWidth == $maxOriginalWidth && $originalHeight == $maxOriginalHeight) {
            echo "Inside if case";
            // If the image is already within the desired dimensions, save it directly
            $uniqueIdentifier = generateUniqueIdentifier();
            $originalFileName = $imageName . 'o_' . $request['prdName'] . '_' . $uniqueIdentifier . '.jpg';
            $originalFilePath = $folderPath . $originalFileName;
	    if (file_put_contents($originalFilePath, $image) === false) {
		  error_log("Failed to save original image: " . $originalFilePath);
                  $response = array("message" => "Failed to save image file", "status" => "error");
                  echo json_encode($response);
                  exit;
            }
        } else {
            // If the image is smaller or equal, resize it to match maxOriginalWidth x maxOriginalHeight
            $newWidth = $maxOriginalWidth;
            $newHeight = $maxOriginalHeight;
        }

        // Create a new image resource with the desired dimensions for the original image
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

        // Resize the original image to the desired dimensions
        imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

        // Generate a unique identifier
        $uniqueIdentifier = generateUniqueIdentifier();

        // Save the resized original image with a unique filename
        $originalFileName = $imageName . 'o_' . $request['prdName'] . '_' . $uniqueIdentifier . '.jpg';
        $originalFilePath = $folderPath . $originalFileName;
        imagejpeg($resizedImage, $originalFilePath);

        // Free up memory
        imagedestroy($resizedImage);

        // Save the paths for the original images
        $originalPaths[$imageName] = 'https://leathers.tamucommerce.in/api/assets/Images/' . $originalFileName;

        // Create the thumbnail with max width 500 and max height 500
        $thumbnailFileName = $imageName . 't_' . $request['prdName'] . '_' . $uniqueIdentifier . '.jpg';
        $thumbnailFilePath = $thumbnailPath . $thumbnailFileName;
        createThumbnail($originalFilePath, $thumbnailFilePath, 500, 500);
        $thumbnailPaths[$imageName] = 'https://leathers.tamucommerce.in/api/assets/thumbnails/' . $thumbnailFileName;
    }



    // Insert or Update data in the database
    $frontViewOriginal = $originalPaths['Front_View'] ?? '';
    $backViewOriginal = $originalPaths['Back_View'] ?? '';
    $topViewOriginal = $originalPaths['Top_View'] ?? '';
    $sideViewOriginal = $originalPaths['Side_View'] ?? '';
    $additionalViewOriginal = $originalPaths['Additional_View'] ?? '';
    $frontViewThumbnail = $thumbnailPaths['Front_View'] ?? '';
    $backViewThumbnail = $thumbnailPaths['Back_View'] ?? '';
    $topViewThumbnail = $thumbnailPaths['Top_View'] ?? '';
    $sideViewThumbnail = $thumbnailPaths['Side_View'] ?? '';
    $additionalViewThumbnail = $thumbnailPaths['Additional_View'] ?? '';


    if ($productId === 0) {

        $query1 = "SELECT IFNULL(MAX(prdId), 0) + 1 AS MaxprdId FROM tblproducts";
        $res = mysqli_query($con, $query1);

        if (!$res) {
            die("Query failed: " . mysqli_error($con));
        }

        $data = mysqli_fetch_array($res);

        if (!$data) {
            die("No data fetched.");
        }

        $prd_Id = $data['MaxprdId'];



        $sql = "INSERT INTO tblproducts(prdId,prdName,prdPrice,prdMrp,prdsubcatId,prdDescription,fvoriginal,bvoriginal,CGST,SGST,HSN,status) VALUES($prd_Id,'$productName',$prdPrice,$mrpPrice,$subCatId,'$prdDescription','$frontViewOriginal','$backViewOriginal',$CGST,$SGST,$HSN,true)";
        if ($con->query($sql) == TRUE) {
            $query = "SELECT ifNull(Max(variantId),0) + 1 As MaxvariantId from tblvariantproduct";

            $res = mysqli_query($con, $query);
            $data = mysqli_fetch_array($res);
            $varient_Id = $data['MaxvariantId'];
            $sql1 = "INSERT INTO tblvariantproduct(variantId,varprdId,varcolorId,varsizeId,varprdPrice,varprdMrp,prdDiscount,stock,fvoriginal,bvoriginal,tvoriginal,svoriginal,avoriginal,fvthumbnail,bvthumbnail,tvthumbnail,svthumbnail,avthumbnail,vartaxPrice) VALUES($varient_Id,$prd_Id,$colorId,$sizeId,$prdPrice,$mrpPrice,$prdDiscount,$stock,'$frontViewOriginal','$backViewOriginal','$topViewOriginal','$sideViewOriginal','$additionalViewOriginal','$frontViewThumbnail','$backViewThumbnail','$topViewThumbnail','$sideViewThumbnail','$additionalViewThumbnail','$taxaddedamount')";

            if ($con->query($sql1) == TRUE) {
                $response['message'] = "product and varient product Successfully uploaded";
                $response['status'] = "success";
                $response['prdId'] =  $prd_Id;
            } else {

                $response['message'] = "Error: " . $sql . "<br>" . mysqli_error($con);
                $response['status'] = "error";
            }
        } else {

            $response[] = array('sts' => false, 'msg' => $sql);
        }
    } elseif ($productId > 0) {
        $sql = "SELECT prdName FROM tblproducts Where prdId = $productId";
        $result = mysqli_query($con, $sql) or die("Error in select products." . mysqli_error($con));

        $row = mysqli_fetch_array($result);
        $prd_Name = $row['prdName'];
        if ($productName != $prd_Name) {
            if ($request['Uploaded_images']) {
                $sql2 = "UPDATE tblproducts SET prdName='$productName',prdPrice=$prdPrice,prdMrp = $mrpPrice,prdDescription ='$prdDescription',prdsubcatId =$subCatId,fvoriginal='$frontViewOriginal',bvoriginal='$backViewOriginal',CGST=$CGST,SGST=$SGST,HSN=$HSN,status='$status' WHERE prdId=$productId";
            } else {

                $sql2 = "UPDATE tblproducts SET prdName='$productName',prdPrice=$prdPrice,prdMrp = $mrpPrice,prdDescription ='$prdDescription',prdsubcatId =$subCatId,CGST=$CGST,SGST=$SGST,HSN=$HSN,status='$status' WHERE prdId=$productId";
            }
            if ($con->query($sql2) == TRUE) {

                $response['message'] = "product  successfully updated";
                $response['status'] = "success";
            } else {


                $response['message'] = "Error in updating product: " . $sql . "<br>" . mysqli_error($con);
                $response['status'] = "error";
            }
        }
        $prd_id = $productId;

        if ($variant_id == null) {
            $query = "SELECT ifNull(Max(variantId),0) + 1 As MaxvariantId from tblvariantproduct";

            $res = mysqli_query($con, $query);
            $data = mysqli_fetch_array($res);
            $varient_Id = $data['MaxvariantId'];

            if ($fvo) {
                $sql1 = "INSERT INTO tblvariantproduct(variantId,varprdId,varcolorId,varsizeId,varprdPrice,varprdMrp,prdDiscount,stock,fvoriginal,bvoriginal,tvoriginal,svoriginal,avoriginal,fvthumbnail,bvthumbnail,tvthumbnail,svthumbnail,avthumbnail) VALUES($varient_Id,$prd_id,$colorId,$sizeId,$prdPrice,$mrpPrice,$prdDiscount,$stock,'$fvo','$bvo','$vto','$svo','$avo','$fvt','$bvt','$tvt','$svt','$avt')";
            } else {

                $sql1 = "INSERT INTO tblvariantproduct(variantId,varprdId,varcolorId,varsizeId,varprdPrice,varprdMrp,prdDiscount,stock,fvoriginal,bvoriginal,tvoriginal,svoriginal,avoriginal,fvthumbnail,bvthumbnail,tvthumbnail,svthumbnail,avthumbnail) VALUES($varient_Id,$prd_id,$colorId,$sizeId,$prdPrice,$mrpPrice,$prdDiscount,$stock,'$frontViewOriginal','$backViewOriginal','$topViewOriginal','$sideViewOriginal','$additionalViewOriginal','$frontViewThumbnail','$backViewThumbnail','$topViewThumbnail','$sideViewThumbnail','$additionalViewThumbnail')";
            }
            if ($con->query($sql1) == TRUE) {


                $response['message'] = "varient product Successfully uploaded";
                $response['status'] = "success";
            } else {


                $response['message'] = "Error in varient product query: " . $sql . "<br>" . mysqli_error($con);
                $response['status'] = "error";
            }
        } else {
            if ($request['Uploaded_images']) {

                $sql = "UPDATE tblvariantproduct SET varprdId =$prd_id, varcolorId= $colorId, varsizeId = $sizeId,varprdPrice =$prdPrice,varprdMrp = $mrpPrice,prdDiscount = $prdDiscount,stock = $stock,fvoriginal = '$frontViewOriginal', bvoriginal = '$backViewOriginal', tvoriginal= '$topViewOriginal', svoriginal = '$sideViewOriginal', avoriginal = '$additionalViewOriginal', fvthumbnail = '$frontViewThumbnail', bvthumbnail = '$backViewThumbnail', tvthumbnail = '$topViewThumbnail', svthumbnail = '$sideViewThumbnail', avthumbnail ='$additionalViewThumbnail' WHERE variantId = $variant_id";
            } else {
                $sql = "UPDATE tblvariantproduct SET varprdId =$prd_id, varcolorId= $colorId, varsizeId = $sizeId,varprdPrice =$prdPrice,varprdMrp = $mrpPrice,prdDiscount = $prdDiscount,stock = $stock WHERE variantId = $variant_id";
            }
            if ($con->query($sql) == TRUE) {


                $response['message'] = "varient product Successfully updated";
                $response['status'] = "success";
            } else {


                $response['message'] = "error occured while update " . $sql . "<br>" . mysqli_error($con);
                $response['status'] = "error";
            }
        }
    }

    echo json_encode($response);
}

function createThumbnail($sourceFile, $destinationFile, $maxWidth, $maxHeight)
{
    list($width, $height, $type) = getimagesize($sourceFile);
    $ratio = min($maxWidth / $width, $maxHeight / $height);
    $newWidth = $width * $ratio;
    $newHeight = $height * $ratio;
    $thumbnail = imagecreatetruecolor($newWidth, $newHeight);

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

    imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    imagejpeg($thumbnail, $destinationFile);
    imagedestroy($source);
    imagedestroy($thumbnail);
}
