<?php
header("Access-Control-Allow-Origin: *");
$url = $_SERVER['REQUEST_URI'];

$url_components = parse_url($url);

// Use parse_str() function to parse the
// string passed via URL
parse_str($url_components['query'], $params);

// Display result
$prdId = $params['prdId'];

require_once 'config.php';
$sql="SELECT
    c.prdId,
    c.usrId,
    c.comment_text,
    c.varId,
    c.date_time,
    r.rating,
    u.userImage,
    u.userName
FROM
    tblcomments c
LEFT JOIN
    tblrating r ON c.prdId = r.prdId AND c.usrId = r.usrId
LEFT JOIN
    tblusers u ON u.userId = c.usrId
WHERE
    c.prdId = $prdId
GROUP BY
    c.usrId
ORDER BY
    c.commentId ASC";






$result = mysqli_query($con, $sql) or die("Error in selecting product: " . mysqli_error($connection));

// Create an array
$comments = array();
while ($row = mysqli_fetch_assoc($result)) {
    $comment = array();
    $comment['prdId'] = $row['prdId'];
    $comment['varId'] = $row['varId'];
    $comment['usrId'] = $row['usrId'];
    $comment['comment_text'] = $row['comment_text'];
    $comment['date_time'] = getTimeAgo($row['date_time']); // Calculate time ago
    $comment['rating'] = $row['rating']; // Add rating
    $comment['userImage'] = $row['userImage'];
    $comment['userName'] = $row['userName'];
    $comments[] = $comment;
}

echo json_encode($comments);
// Function to calculate time ago
function getTimeAgo($dateTime) {
    $timestamp = strtotime($dateTime);
    $currentTimestamp = time();
    $seconds = $currentTimestamp - $timestamp;
    
    $minutes = floor($seconds / 60); // 50 minutes
    $hours = floor($seconds / 3600); // 50 minutes / 60 = 0.83 hours (rounded down to 0 hours)
    $days = floor($seconds / 86400); // 50 minutes / 60 / 24 = 0.0347 days (rounded down to 0 days)
    $months = floor($seconds / (86400 * 30)); // 50 minutes / 60 / 24 / 30 = 0.0011 months (rounded down to 0 months)
    $years = floor($seconds / (86400 * 365)); // 50 minutes / 60 / 24 / 365 = 0.00013 years (rounded down to 0 years)
    
    if ($seconds < 60) {
        return $seconds . " seconds ago";
    } elseif ($minutes < 60) {
        return $minutes . " minutes ago";
    } elseif ($hours < 24) {
        return $hours . " hours ago";
    } elseif ($days < 30) {
        return $days . " days ago";
    } elseif ($months < 12) {
        return $months . " months ago";
    } else {
        return $years . " years ago";
    }
}

?>
