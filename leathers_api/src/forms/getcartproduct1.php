<?php
    header("Access-Control-Allow-Origin: *");

    $url = $_SERVER['REQUEST_URI'];

    $url_components = parse_url($url);

    // Use parse_str() function to parse the
    // string passed via URL
    parse_str($url_components['query'], $params);
         
    // Display result
     $usrId = $params['usrId'];
     $ordId = $params['ordId'];
    //open connection to mysql db
    require_once 'config.php';
   if($usrId){
   $sql ="SELECT ordId,orddetQty,ordStatus,variantId,
   v.fvoriginal AS fvoriginal,
   orddetPrdPrice AS varprdPrice,
   v.prdDiscount,
   varsizeId,
   varcolorId
   prdName,
   colorName,
   sizeName FROM tblorders 
   LEFT JOIN tblorderDetails ON orddetOrder = ordId
   LEFT JOIN tblvariantproduct v ON v.variantId= orddetProduct 
   LEFT JOIN tblproducts ON prdId = v.varprdId 
   LEFT JOIN tblcolors ON colorId = orddetColor 
   LEFT JOIN tblsize ON sizeId = orddetSize 
   WHERE ordCustomer = $usrId";
   
   }else if ($ordId){
   $sql ="SELECT ordId,orddetQty,ordStatus,variantId,v.fvoriginal AS fvoriginal, v.prdDiscount,orddetPrdPrice AS varprdPrice,varsizeId,varcolorId,prdName,colorName,sizeName FROM tblorders 
   LEFT JOIN tblorderDetails ON orddetOrder = ordId
   LEFT JOIN tblvariantproduct v ON v.variantId= orddetProduct 
   LEFT JOIN tblproducts ON prdId = v.varprdId
   LEFT JOIN tblcolors ON colorId = orddetColor
   LEFT JOIN tblsize ON sizeId = orddetSize 
   WHERE ordId = $ordId";
   }
    $result = mysqli_query($con, $sql) or die("Error in Selecting " . mysqli_error($con));

    //create an array
    $cartarray = array();
    while($row =mysqli_fetch_assoc($result))
    {
        $cartarray[] = $row;
    }
    
    echo json_encode($cartarray);
?>