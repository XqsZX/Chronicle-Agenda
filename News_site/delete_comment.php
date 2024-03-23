<?php
session_start();
require 'config.php';

// check user is login or not
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && isset($_GET["id"])) {
    $comment_id = $_GET["id"];
    $user_id = $_SESSION["user_id"];

    // Get story id first
    $story_id = 0;
    $query = "SELECT story_id FROM comments WHERE comment_id = ?";
    if($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $comment_id);
        $stmt->execute();
        $stmt->bind_result($story_id);
        $stmt->fetch();
        $stmt->close();
    }

    // check the comment is user's or not
    $sql = "DELETE FROM comments WHERE comment_id = ? AND user_id = ?";
    if($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $comment_id, $user_id);
        if($stmt->execute()) {
            // delete successfully
            header("Location: story_detail.php?id=" . $story_id);
            exit();
        } else {
            echo "An error occurred. Please try again later.";
        }
        $stmt->close();
    }
} else {
    // user did not login
    header("location: login.html");
    exit();
}

$conn->close();
?>

