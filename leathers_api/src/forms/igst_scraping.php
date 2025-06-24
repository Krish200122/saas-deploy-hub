<?php

// Ensure that the HSN code is provided in the query string
// if (empty($_GET['hsnCode'])) {
//     die('HSN code not provided.');
// }

// $hsnCode = $_GET['hsnCode'];
$hsnCode = '9506';
$cleartaxUrl = 'https://cleartax.in/s/gst-hsn-lookup';

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $cleartaxUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

// Execute the cURL session and fetch the page content
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    die('cURL Error: ' . curl_error($ch));
}

// Close the cURL session
curl_close($ch);

// Parse the page content using DOMDocument
$dom = new DOMDocument();
libxml_use_internal_errors(true); // Suppress parsing errors
$dom->loadHTML($response);
libxml_clear_errors();

// Find the HSN input field and the search button
$inputField = $dom->getElementById('input');
$searchButton = $dom->getElementsByClassName('text-base');

// Fill the HSN Code input field with the provided HSN code
$inputField->setAttribute('value', $hsnCode);

// Simulate a form submission by triggering the "click" event of the search button

$script = "
    var clickEvent = new MouseEvent('click', {
        'view': window,
        'bubbles': true,
        'cancelable': true
    });
    document.querySelector('.text-base').dispatchEvent(clickEvent);
";
$scriptTag = $dom->createElement('script', $script);
$dom->getElementsByTagName('body')->item(0)->appendChild($scriptTag);

// Wait for a short time to allow the page to load after the "click" event
sleep(2);

// Find the IGST rate element
$igstRateElements = $dom->getElementsByClassName('text-s-14');

// Extract the IGST rate from the first matching element
$igstRate = count($igstRateElements) > 0 ? $igstRateElements[0]->nodeValue : 'IGST rate not found for HSN Code ' . $hsnCode;

// Return the IGST rate as a JSON response
header('Content-Type: application/json');
echo json_encode(['igstRate' => $igstRate]);
