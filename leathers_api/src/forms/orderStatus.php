<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    $jsonEmail = file_get_contents('php://input');
    $jsonEmail = html_entity_decode($jsonEmail);
    $Mail = json_decode($jsonEmail);
    
    $ordId = $Mail->{'ordId'};
    $ordStatus = $Mail->{'ordStatus'};
    
        //open connection to mysql db
        require_once 'config.php';
    
    
        $sql = "UPDATE tblorders SET ordState = $ordStatus WHERE ordId = $ordId";
        
        if ($con->query($sql) === TRUE) {
              $msg = "New record created successfully";
            } else {
              $msg = "Error: " . $sql . "<br>" . $con->error;
        }
         $sql = "SELECT ordId,ordCustomer,ordState,ordDateTime,netPrice,userName,userEmail,ordstatusDescription, uadAddress, uadCountry, uadState, uadDistrict, uadPincode, uadPhoneno, orddetQty, orddetProduct, prdName, prdPrice
            FROM tblorders
            LEFT JOIN tblusers ON userId = ordCustomer
            LEFT JOIN tblorderStatus ON ordstatusid = ordStatus
            LEFT JOIN tblusersAddresses ON useraddId = ordCustomer
            LEFT JOIN tblorderDetails ON orddetOrder = ordId
            LEFT JOIN tblproducts ON prdId = orddetProduct
            WHERE ordId = $ordId";
            

         // Execute the query
         $result = mysqli_query($con, $sql) or die("Error in Selecting " . mysqli_error($connection));
          $orderObj = array();
             while ($row = mysqli_fetch_assoc($result)) {
                 $orderObj[] = $row;
             }
        //$emailId = $orderObj['userEmail'];
        

        use PHPMailer\PHPMailer\PHPMailer;
        use PHPMailer\PHPMailer\Exception;
        
        require './PHPMailer/src/Exception.php';
        require './PHPMailer/src/PHPMailer.php';
        require './PHPMailer/src/SMTP.php';
        $mail = new PHPMailer(true); 
        try {
            $mail->IsSMTP();                           
            $mail->SMTPAuth = false;                 
            $mail->Port = 25;                    
            $mail->Host = "localhost"; 
            $mail->Username = "tamu2023@tamucommerce.in";   
            $mail->Password = "Tamu@2023";            
        
            $mail->IsSendmail();  
        
            $mail->From = "tamu2023@tamucommerce.in";
            $mail->FromName = "tamucommerce.com";
        
            // Fetch email addresses from the database
           
                //$emailId = $orderObj['userEmail'];
                $emailId = $orderObj[0]['userEmail'];
                
                // Compose the email content using order details from $orderObj
                $message = "<h2>Dear {$orderObj[0]['userName']},</h2>";
                $message .= "<p>Thank you for your order. We are pleased to inform you that your order (Order ID: {$orderObj[0]['ordId']}) is,</p>";
            
                // Determine the status description and compose message accordingly
                switch ($orderObj[0]['ordstatusDescription']) {
                    case 'Ordered':
                        $message .= "<p>Your order (Order ID: {$orderObj[0]['ordId']}) is currently being processed.</p>";
                        break;
                    case 'In-progress':
                        $message .= "<p>Your order (Order ID: {$orderObj[0]['ordId']}) is now in progress.</p>";
                        break;
                    case 'Dispatched':
                        $message .= "<p>Your order (Order ID: {$orderObj[0]['ordId']}) has been dispatched and is on its way to you.</p>";
                        break;
                    case 'Delivered':
                        $message .= "<p>Your order (Order ID: {$orderObj[0]['ordId']}) has been successfully delivered. We hope you enjoy your purchase!</p>";
                        break;
                    case 'Cancelled':
                        $message .= "<p>Unfortunately, your order (Order ID: {$orderObj[0]['ordId']}) has been cancelled.</p>";
                        break;
                    case 'Failed':
                        $message .= "<p>We encountered an issue processing your order (Order ID: {$orderObj[0]['ordId']}). Please contact our support for assistance.</p>";
                        break;
                    default:
                        $message .= "<p>Your order status: {$orderObj[0]['ordstatusDescription']}</p>";
                        break;
                }
            
                $message .= "<p><strong>Order Details:</strong></p>";
                $message .= "<ul>";
                foreach ($orderObj as $orderItem) {
                    $message .= "<li>{$orderItem['orddetQty']} x {$orderItem['prdName']} - Price: {$orderItem['prdPrice']}</li>";
                }
                $message .= "</ul>";
                $message .= "<p><strong>Shipping Address:</strong></p>";
                $message .= "<p>{$orderObj[0]['uadAddress']}</p>";
                $message .= "<p>{$orderObj[0]['uadDistrict']}, {$orderObj[0]['uadState']} - {$orderObj[0]['uadPincode']}</p>";
                $message .= "<p>{$orderObj[0]['uadCountry']}</p>";
                $message .= "<p><strong>Total Amount:</strong> {$orderObj[0]['netPrice']}</p>";

                $mail->ClearAddresses();
                $mail->AddAddress($emailId);
                $mail->addReplyTo('kkathiravan709@gmail.com', 'Information');
                $mail->Subject = 'your order status in our products';
                $mail->WordWrap = 80;
                $mail->MsgHTML($message);
                $mail->IsHTML(true);
        
                if (!$mail->Send()) {
                    $msg = "Failed to send email to $emailId";
                } else {
                    $msg = "Email sent to $emailId ...!!";
                }
            
        
        } catch (Exception $e) {
            $msg = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
         echo json_encode($msg);