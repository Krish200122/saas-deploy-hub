<?php

header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");
header("Access-Control-Allow-Origin: *");

// Include required files
require_once("./lib/config_paytm.php");
require_once("./lib/encdec_paytm.php");
require_once 'config.php';

// Initialize message
$msg = "";

// Validate DB connection
if (mysqli_connect_errno()) {
    die('Failed to connect to MySQL: ' . mysqli_connect_error());
}

//$paytmChecksum = isset($_POST["CHECKSUMHASH"]) ? $_POST["CHECKSUMHASH"] : ""; // From Paytm
$paytmChecksum = "";
$paramList = array();
$isValidChecksum = "FALSE";

$paramList = $_POST;
$paytmChecksum = isset($_POST["CHECKSUMHASH"]) ? $_POST["CHECKSUMHASH"] : ""; //Sent by Paytm pg
$isValidChecksum = verifychecksum_e($paramList, PAYTM_MERCHANT_KEY, $paytmChecksum); // returns TRUE or FALSE
//if ($isValidChecksum == "TRUE") {
    if (!empty($_POST)) {

        // Safely access all keys to avoid undefined index warnings
        $CURRENCY = $_POST["CURRENCY"] ?? '';
        $GATEWAYNAME = $_POST["GATEWAYNAME"] ?? '';
        $RESPMSG = $_POST["RESPMSG"] ?? '';
        $PAYMENTMODE = $_POST["PAYMENTMODE"] ?? '';
        $TXNID = $_POST["TXNID"] ?? '';
        $TXNAMOUNT = $_POST["TXNAMOUNT"] ?? '';
        $ORDERID = $_POST["ORDERID"] ?? '';
        $STATUS = $_POST["STATUS"] ?? '';
        $BANKTXNID = $_POST["BANKTXNID"] ?? '';
        $TXNDATE = $_POST["TXNDATE"] ?? '';

        $ordarray = explode("_", $ORDERID);
        $ordId = $ordarray[0] ?? '';

        if ($STATUS == "TXN_SUCCESS") {
            $sql = "UPDATE `tblorders` SET `ordState`= 1, `ordTotal`='$TXNAMOUNT', `transactionId`='$TXNID' WHERE `ordId`='$ordId'";
		
            if ($con->query($sql) === TRUE) {
                $msg .= "Order detail created / updated successfully. ";
		 $sqlUpdateDetails = "UPDATE `tblorderDetails` SET `ordStatus` = 1 WHERE `orddetOrder` = '$ordId'";
    		if ($con->query($sqlUpdateDetails) === TRUE) {
       			 $msg .= "Order details updated successfully. ";
   		 } else {
      			  $msg .= "Failed to update order details: " . $con->error;
    			}
                

                $sql1 = "SELECT * FROM tblorderDetails WHERE `orddetOrder`='$ordId'";
                $result = mysqli_query($con, $sql1);
                if (!$result) {
                    die("Error in select products: " . mysqli_error($con1));
                }

                $product = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $product[] = $row;
                }

                foreach ($product as $item) {
                    $varId = $item['orddetProduct'];
                    $stock = $item['orddetQty'];
                    $sql2 = "UPDATE `tblvariantproduct` SET `stock`=stock - $stock WHERE `variantId`='$varId'";
                    if ($con->query($sql2) === TRUE) {
                        $msg .= "Stock updated successfully for variant ID $varId. ";
                    } else {
                        $msg .= "Error updating stock for variant ID $varId: " . $con->error . " ";
                    }
                }

            } else {
                $msg .= "Error updating order: " . $con->error;
            }

        } else {
            // Transaction failed
        $sqlFailOrder = "UPDATE `tblorders` SET `ordState` = 6 WHERE `ordId` = '$ordId'";
        $sqlFailDetails = "UPDATE `tblorderDetails` SET `ordStatus` = 6 WHERE `orddetOrder` = '$ordId'";

        if ($con->query($sqlFailOrder) === TRUE && $con->query($sqlFailDetails) === TRUE) {
            $msg .= "Order marked as failed.";
        } else {
            $msg .= "Failed to mark order as failed: " . $con->error;
        }
        }
    }

    // Print or log the message
    //echo $msg;

//} else {
//    echo "<b>Checksum mismatched.</b>";
    // Consider logging for suspicious transactions
//}

?>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title> Transactional Details</title>
  <style>
    /* -------------------------------------
          GLOBAL RESETS
      ------------------------------------- */

    /*All the styling goes here*/

    img {
      border: none;
      -ms-interpolation-mode: bicubic;
      max-width: 100%;
    }

    body {
      background-color: #f6f6f6;
      font-family: sans-serif;
      -webkit-font-smoothing: antialiased;
      font-size: 14px;
      line-height: 1.4;
      margin: 0;
      padding: 0;
      -ms-text-size-adjust: 100%;
      -webkit-text-size-adjust: 100%;
    }

    table {
      border-collapse: separate;
      mso-table-lspace: 0pt;
      mso-table-rspace: 0pt;
      width: 100%;
    }

    table td {
      font-family: sans-serif;
      font-size: 14px;
      vertical-align: top;
    }

    /* -------------------------------------
          BODY & CONTAINER
      ------------------------------------- */

    .body {
      background-color: #f6f6f6;
      width: 100%;
    }

    /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
    .container {
      display: block;
      margin: 0 auto !important;
      /* makes it centered */
      max-width: 580px;
      padding: 10px;
      width: 580px;
    }

    /* This should also be a block element, so that it will fill 100% of the .container */
    .content {
      box-sizing: border-box;
      display: block;
      margin: 0 auto;
      max-width: 580px;
      padding: 10px;
    }

    /* -------------------------------------
          HEADER, FOOTER, MAIN
      ------------------------------------- */
    .main {
      background: #ffffff;
      border-radius: 3px;
      width: 100%;
    }

    .wrapper {
      box-sizing: border-box;
      padding: 20px;

    }

    .content-block {
      padding-bottom: 10px;
      padding-top: 10px;
    }

    .footer {
      clear: both;
      margin-top: 10px;
      text-align: center;
      width: 100%;
    }

    .footer td,
    .footer p,
    .footer span,
    .footer a {
      color: #999999;
      font-size: 12px;
      text-align: center;
    }

    /* -------------------------------------
          TYPOGRAPHY
      ------------------------------------- */
    h1,
    h2,
    h3,
    h4 {
      color: #000000;
      font-family: sans-serif;
      font-weight: 400;
      line-height: 1.4;
      margin: 0;
      margin-bottom: 30px;
    }

    h1 {
      font-size: 35px;
      font-weight: 300;
      text-align: center;
      text-transform: capitalize;
    }

    p,
    ul,
    ol {
      font-family: sans-serif;
      font-size: 14px;
      font-weight: normal;
      margin: 0;
      margin-bottom: 15px;
    }

    p li,
    ul li,
    ol li {
      list-style-position: inside;
      margin-left: 5px;
    }

    a {
      color: #3498db;
      text-decoration: underline;
    }

    /* -------------------------------------
          BUTTONS
      ------------------------------------- */
    .btn {
      box-sizing: border-box;
      width: 100%;
    }

    .btn>tbody>tr>td {
      padding-bottom: 15px;
    }

    .btn table {
      width: auto;
    }

    .btn table td {
      background-color: #ffffff;
      border-radius: 5px;
      text-align: center;
    }

    .btn a {
      background-color: #ffffff;
      border: solid 1px #3498db;
      border-radius: 5px;
      box-sizing: border-box;
      color: #3498db;
      cursor: pointer;
      display: inline-block;
      font-size: 14px;
      font-weight: bold;
      margin: 0;
      padding: 12px 25px;
      text-decoration: none;
      text-transform: capitalize;
    }

    .btn-primary table td {
      background-color: #3498db;
    }

    .btn-primary a {
      background-color: black;
      border-color: #3498db;
      color: #ffffff;
    }

    /* -------------------------------------
          OTHER STYLES THAT MIGHT BE USEFUL
      ------------------------------------- */
    .last {
      margin-bottom: 0;
    }

    .first {
      margin-top: 0;
    }

    .align-center {
      text-align: center;
    }

    .align-right {
      text-align: right;
    }

    .align-left {
      text-align: left;
    }

    .clear {
      clear: both;
    }

    .mt0 {
      margin-top: 0;
    }

    .mb0 {
      margin-bottom: 0;
    }

    .preheader {
      color: transparent;
      display: none;
      height: 0;
      max-height: 0;
      max-width: 0;
      opacity: 0;
      overflow: hidden;
      mso-hide: all;
      visibility: hidden;
      width: 0;
    }

    .powered-by a {
      text-decoration: none;
    }

    hr {
      border: 0;
      border-bottom: 1px solid #f6f6f6;
      margin: 20px 0;
    }

    /* -------------------------------------
          RESPONSIVE AND MOBILE FRIENDLY STYLES
      ------------------------------------- */
    @media only screen and (max-width: 620px) {
      table.body h1 {
        font-size: 28px !important;
        margin-bottom: 10px !important;
      }

      table.body p,
      table.body ul,
      table.body ol,
      table.body td,
      table.body span,
      table.body a {
        font-size: 16px !important;
      }

      table.body .wrapper,
      table.body .article {
        padding: 10px !important;
      }

      table.body .content {
        padding: 0 !important;
      }

      table.body .container {
        padding: 0 !important;
        width: 100% !important;
      }

      table.body .main {
        border-left-width: 0 !important;
        border-radius: 0 !important;
        border-right-width: 0 !important;
      }

      table.body .btn table {
        width: 100% !important;
      }

      table.body .btn a {
        width: 100% !important;
      }

      table.body .img-responsive {
        height: auto !important;
        max-width: 100% !important;
        width: auto !important;
      }
    }

    /* -------------------------------------
          PRESERVE THESE STYLES IN THE HEAD
      ------------------------------------- */
    @media all {
      .ExternalClass {
        width: 100%;
      }

      .ExternalClass,
      .ExternalClass p,
      .ExternalClass span,
      .ExternalClass font,
      .ExternalClass td,
      .ExternalClass div {
        line-height: 100%;
      }

      .apple-link a {
        color: inherit !important;
        font-family: inherit !important;
        font-size: inherit !important;
        font-weight: inherit !important;
        line-height: inherit !important;
        text-decoration: none !important;
      }

      #MessageViewBody a {
        color: inherit;
        text-decoration: none;
        font-size: inherit;
        font-family: inherit;
        font-weight: inherit;
        line-height: inherit;
      }

      .btn-primary table td:hover {
        background-color: #34495e !important;
      }

      .btn-primary a:hover {
        background-color: #34495e !important;
        border-color: #34495e !important;
      }
    }
  </style>
</head>

<body>
  <span class="preheader">This is preheader text. Some clients will show this text as a preview.</span>
  <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
    <tr>
      <td>&nbsp;</td>
      <td class="container">
        <div class="content">

          <!-- START CENTERED WHITE CONTAINER -->
          <table role="presentation" class="main">

            <!-- START MAIN CONTENT AREA -->
            <tr>
              <td class="wrapper">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td>
                      <?php
                      echo '<p>Hi ' . $UserName . ',</p>';
                      ?>
                      <div style="background-color:aliceblue;align-items:center;">
                        <?php
                        if ($STATUS == "TXN_SUCCESS") {
                          echo '<img src="https://leathers.tamucommerce.in/api/assets/Images/thankyou.png" style="margin-left:27%;" width="250" height="500"><br>';
                          echo '<ul>';
                          echo '<li style="font-family:serif">TXN ID = ' . $TXNID . '</li></br>';
                          echo '<li style="font-family:serif">AMOUNT = ' . $TXNAMOUNT . '</li></br>';
                          echo '<li style="font-family:serif">TXN STATUS = ' . $STATUS . '</li></br>';
                          echo '<li style="font-family:serif">TXN DATE = ' . $TXNDATE . '</li></br>
                           </ul>';
                        } else {
                          echo '<img src="https://leathers.tamucommerce.in/api/assets/Images/failiure.png" style="margin-left:27%;" width="250" height="200">';
                        }

                        ?>
                      </div>
                      <?php
                      if ($STATUS == "TXN_SUCCESS") {
                        echo '<p>Your transaction completed successfully .</p>';
                      } else {
                        echo '<p>Your transaction failed please try again .</p>';
                      }
                      ?>
                      <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                        <tbody>
                          <tr>
                            <td align="left">
                              <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tbody>
                                  <tr>
                                    <td> <a href="https://leathers.tamucommerce.in/#/" target="_blank">Continue To
                                        Site</a> </td>
                                  </tr>
                                </tbody>
                              </table>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>

            <!-- END MAIN CONTENT AREA -->
          </table>
          <!-- END CENTERED WHITE CONTAINER -->

          <!-- START FOOTER -->
          <div class="footer">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td class="content-block">
                  <span class="apple-link">zuna@gmail.com</span>
                </td>
              </tr>
            </table>
          </div>
          <!-- END FOOTER -->
        </div>
      </td>
      <td>&nbsp;</td>
    </tr>
  </table>
</body>

</html>;