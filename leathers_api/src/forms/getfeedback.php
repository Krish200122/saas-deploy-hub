<?php

header("Access-Control-Allow-Origin: *");

$url = $_SERVER['REQUEST_URI'];

$url_components = parse_url($url);

// Use parse_str() function to parse the
// string passed via URL
parse_str($url_components['query'], $params);

// Display result
$prdId = $params['prdId'];

// Open connection to MySQL database
require_once 'config.php';
// Fetch table rows from MySQL database
$sql = "SELECT c.prdId, c.usrId, c.commentId, c.comment_text, r.replyId, r.reply_text, r.reply_usrId
        FROM tblcomments c
        LEFT JOIN tblreplies r ON c.commentId = r.reply_commentId 
        WHERE c.prdId = $prdId 
        ORDER BY c.commentId ASC"; // Added "ORDER BY c.commentId ASC" to sort by commentId

$result = mysqli_query($con, $sql) or die("Error in selecting product: " . mysqli_error($con));

// Create an array for comments
$comments = array();

while ($row = mysqli_fetch_assoc($result)) {
    $commentId = $row['commentId'];
    
    // If the comment doesn't exist in the comments array, create a new comment object
    if (!isset($comments[$commentId])) {
        $comments[$commentId] = array(
            'prdId' => $row['prdId'],
            'usrId' => $row['usrId'],
            'commentId' => $row['commentId'],
            'comment_text' => $row['comment_text'],
            'replies' => array() // Initialize an empty array for replies
        );
    }
    
    // Add the reply object to the respective comment's replies array
    $comments[$commentId]['replies'][] = array(
        'replyId' => $row['replyId'],
        'reply_text' => $row['reply_text'],
        'reply_usrId' => $row['reply_usrId']
    );
}

// Convert the comments array to a simple array structure
$commentArray = array_values($comments);

echo json_encode($commentArray);

?>
