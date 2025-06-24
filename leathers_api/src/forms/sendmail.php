<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
$jsonEmail = file_get_contents('php://input');
$jsonEmail = html_entity_decode($jsonEmail);
$Mail = json_decode($jsonEmail);

$subject = $Mail->{'subject'};
$message = $Mail->{'message'};

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
    require_once 'config.php';
    $sql = "SELECT email FROM `tblnewsletter`";
    $result = mysqli_query($con, $sql) or die("Error in selecting email addresses: " . mysqli_error($con));

    while ($row = mysqli_fetch_assoc($result)) {
        $emailId = $row['email'];
        $mail->ClearAddresses();
        $mail->AddAddress($emailId);
        $mail->addReplyTo('kkathiravan709@gmail.com', 'Information');
        $mail->Subject = $subject;
        $mail->WordWrap = 80;
        $mail->MsgHTML($message);
        $mail->IsHTML(true);

        if (!$mail->Send()) {
            echo "Failed to send email to $emailId";
        } else {
            echo "Email sent to $emailId ...!!";
        }
    }

    mysqli_close($con);

} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
