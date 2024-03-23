<?php
session_start();
require 'config.php';

// Check user is login or not
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // Store the story_id to get back to it after login
    $story_id = isset($_POST['story_id']) ? $_POST['story_id'] : 0;
    
    // If story_id is valid, Store it in session for further redirect
    if ($story_id) {
        $_SESSION['redirect_after_login'] = 'story_detail.php?id=' . $story_id;
    }

    // If user is not login, redirect to login page
    header("location: login.html");
    exit;
}

// Check CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo "CSRF token validation failed.";
    exit;
}

$story_id = $_POST['story_id'];
$vote_type = $_POST['vote_type'];
$user_id = $_SESSION['user_id'];

// Check the user has voted or not
$query = "SELECT vote_id FROM article_votes WHERE story_id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $story_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // If user voted, update the vote_type
    $row = $result->fetch_assoc();
    $updateQuery = "UPDATE article_votes SET vote_type = ? WHERE vote_id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("si", $vote_type, $row['vote_id']);
} else {
    // If user haven't voted, insert new vote
    $insertQuery = "INSERT INTO article_votes (story_id, user_id, vote_type) VALUES (?, ?, ?)";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("iis", $story_id, $user_id, $vote_type);
}

if (isset($updateStmt) && !$updateStmt->execute()) {
    echo "An error occurred updating your vote.";
} elseif (isset($insertStmt) && !$insertStmt->execute()) {
    echo "An error occurred inserting your vote.";
} else {
    // Vote successfully, redirect to story_detail
    header("Location: story_detail.php?id=" . $story_id);
    exit;
}

// Close the connection to SQL
if (isset($updateStmt)) $updateStmt->close();
if (isset($insertStmt)) $insertStmt->close();
$conn->close();
?>

