<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");        

$jsonValue = file_get_contents('php://input');
$jsonValue = html_entity_decode($jsonValue);
$value = json_decode($jsonValue);     
require_once 'config.php';
// $subCategoryId = $Size['subcategoryid'];

$subCategoryId = isset($value->subcategoryid) ? (int)$value->subcategoryid : 0;
$sizeArray = isset($value->size) ? $value->size : [];

// echo json_encode($sizeArray);
$sql = "SELECT * FROM tblsize WHERE sizesubcatId = $subCategoryId ";
$result = mysqli_query($con,$sql) or die("Error in selecting sizes." . mysqli_error($conection));

//create an array
$productsarray = array();

 while($row =mysqli_fetch_assoc($result))
{
    $productsarray[] = $row;
}
// echo json_encode($productsarray);  //at starting
foreach ($sizeArray as $size) {
    // Trim whitespace from the size value (optional)
    $size = trim($size);
    foreach ($productsarray as $key => $product) {
        
        if ($size == $product['sizeName']) {
            // Remove the product from $productsarray
            unset($productsarray[$key]);

            // Remove the size from $sizearray
            $sizeKey = array_search($size, $sizeArray);
            if ($sizeKey !== false) {
                unset($sizeArray[$sizeKey]);
            }
        } else {
            
            $sql = "SELECT 1 from tblsize WHERE sizesubcatId = $subCategoryId and sizeName ='$size'";
            $result = mysqli_num_rows(mysqli_query($con, $sql));

            if (mysqli_num_rows(mysqli_query($con, $sql)) > 0) {
                $msg = "Size already Exists";
            }else{
            $query = "SELECT IFNULL(MAX(sizeId), 0) + 1 AS MaxsizeId FROM tblsize";
            $res = mysqli_query($con, $query);
            $data = mysqli_fetch_array($res);
            $size_Id = $data['MaxsizeId'];
            
            // Insert a new row for $sizeName into the database
            $Sql2 = "INSERT INTO tblsize (sizeId, sizesubcatId, sizeName) VALUES ($size_Id, $subCategoryId, '$size')";
           
            if ($con->query($Sql2) === TRUE) {
                $msg = "New Size added successfully";
               
            } else {
                $msg = "Error in adding New Size: " . $Sql2 . "<br>" . $con->error;
                 
            }

            // Remove the size from $sizearray
            $sizeKey = array_search($size, $sizeArray);
            if ($sizeKey !== false) {
                unset($sizeArray[$sizeKey]);
            }
            }
        }
    }
}
$productsarray = array_values($productsarray);
// If $sizearray is empty, delete remaining rows from the database
if (empty($sizeArray) && !empty($productsarray)) {
    foreach ($productsarray as $product) {
        $deleteSql = "DELETE FROM tblsize WHERE sizeId = " . $product['sizeId'];
        if ($con->query($deleteSql) === TRUE) {
                $msg =array('status' => 'success', 'message' => 'Data inserted successfully');
                 
            } else {
                $msg = "Error in deleting removed Size: " . $deleteSql . "<br>" . $con->error;
                 
            }
    }
}
echo json_encode($msg);
?>