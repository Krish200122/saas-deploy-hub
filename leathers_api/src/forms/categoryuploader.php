<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

$folderPath = "../assets/images/";
$postdata = file_get_contents("php://input");
if (!empty($postdata)) {
    $request = json_decode($postdata);
    $image_parts = explode(";base64,", $request->image);
    $productId = $request->{'productId'};
    $productName = $request->{'productName'};
    $image_name = $request->{'productName'};
    $image_name = preg_replace('/\s+/', '_', $image_name);
    $image_type_aux = explode("image/", $image_parts[0]);
    $image_base64 = base64_decode($image_parts[1]);
    $file = $folderPath . $image_name . '.jpg';
    if (file_put_contents($file, $image_base64)) {

        require_once 'config.php';

        if ($productId == 0) {

            $query = "SELECT ifNull(Max(catId),0) + 1 As MaxcatId from tblcategories";

                $res = mysqli_query($con, $query);
                $data = mysqli_fetch_array($res);
                $catId = $data['MaxcatId'];

                $sql = "INSERT INTO tblcategories(catId,catName,catImage) VALUES($catId,'$productName','https://tamucommerce.in/assets/images/$image_name.jpg')";

                if ($con->query($sql) == TRUE) {

                    $response[] = array('sts' => true, 'msg' => 'Successfully uploaded');

                } else {

                    $response[] = array('sts' => false, 'msg' => 'Please upload Image error in upload');

                }

        }  elseif($productId > 0) {

            $sql = "UPDATE tblcategories SET catName = '$productName', catImage = 'https://tamucommerce.in/assets/images/$image_name.jpg' WHERE catId =" . $productId;

            if ($con->query($sql) == TRUE) {

                $response[] = array('sts' => true, 'msg' => 'Successfully uploaded');

            } else {

                $response[] = array('sts' => false, 'msg' => 'Please upload Image');

            }
        }else {

            $con = mysqli_connect("localhost", "sqladmin1", "P@ssw0rd12345", "Ecommerce_Leathers") or die("Error in connection." . mysqli_error($conection));
            $sql = "UPDATE tblcategories SET catName = '$productName'  WHERE catId =" . $productId;
    
                    if ($con->query($sql) == TRUE) {
    
                        $response[] = array('sts' => true, 'msg' => 'Successfully uploaded');
    
                    } else {
    
                        $response[] = array('sts' => false, 'msg' => 'Please upload Image');
    
                    }
            //$response[] = array('sts'=>false,'msg'=>'Please upload Image');
    
        }

    } else {

       $con = mysqli_connect("localhost", "sqladmin1", "P@ssw0rd12345", "Ecommerce_Leathers") or die("Error in connection." . mysqli_error($conection));
        $sql = "UPDATE tblcategories SET catName = '$productName' WHERE catId =" . $productId;

                if ($con->query($sql) == TRUE) {

                    $response[] = array('sts' => true, 'msg' => 'Successfully uploaded');

                } else {

                    $response[] = array('sts' => false, 'msg' => 'Please upload Image');

                }
        //$response[] = array('sts'=>false,'msg'=>'Please upload Image');

    }
    echo json_encode($response);
}
?>