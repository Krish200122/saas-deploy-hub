<?php

   header("Access-Control-Allow-Origin: *");

    $url = $_SERVER['REQUEST_URI'];

    $url_components = parse_url($url);

    // Use parse_str() function to parse the
    // string passed via URL
    parse_str($url_components['query'], $params);
         
    // Display result
    $prdId = $params['prdId'];
    
    //open connection to mysql db
    require_once 'config.php';
     //fetch table rows from mysql db 
   $sql = "SELECT * FROM tblspecification WHERE specprdId = $prdId";
    $result = mysqli_query($con, $sql) or die("Error in select product." . mysqli_error($connection));

    //create an array
    $productspec = array();
    while($row =mysqli_fetch_assoc($result))
    {
        $productspec[] = $row;
    }
    $specdata = [];

    foreach ($productspec as $item) {
        $specId = $item['specId'];
        $specprdId = $item['specprdId'];
    
        // Initialize the transformed item
        // $transformedItem = [
        //     'specId' => $specId,
        //     'specprdId' => $specprdId
        // ];
    
        // Iterate over the remaining properties and apply the transformation
        for ($i = 1; $i <= 5; $i++) {
            $specKey = 'spec' . $i;
            $valueKey = 'value' . $i;
            if (isset($item[$specKey]) && isset($item[$valueKey])) {
                $transformedItem[$item[$specKey]] = $item[$valueKey];
            }
        }
    
        // Add the transformed item to the specdata array
        $filteredItem = array_filter($transformedItem, function ($value) {
        return !empty($value);
    });

    $specdata[] = $filteredItem;
    $specdata[] = $specId;

    }

    echo json_encode($specdata);
    
?>