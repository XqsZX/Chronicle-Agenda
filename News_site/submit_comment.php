<?php
session_start();
require 'config.php';

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    $story_id = $_POST['story_id'];
    $comment = $_POST['comment'];
    $user_id = $_SESSION["user_id"];

    // Insert comment into mysql
    $sql = "INSERT INTO comments (story_id, user_id, comment) VALUES (?, ?, ?)";
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("iis", $story_id, $user_id, $comment);
        
        if($stmt->execute()){
            // comment success, redirect to story detail
            header("Location: story_detail.php?id=" . $story_id);
            exit();
        } else{
            echo "Something went wrong. Please try again later.";
        }
    }

    $stmt->close();
}

$conn->close();
?>

