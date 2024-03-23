<?php
session_start();
require 'config.php';

// check user is login or not
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_GET["id"])) {
    header("location: login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // check CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }
}

$story_id = $_GET["id"];
$user_id = $_SESSION["user_id"];

// check if this user can delete this story or not
$sql = "SELECT user_id FROM stories WHERE story_id = ?";
if($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $story_id);
    $stmt->execute();
    $stmt->store_result();
    
    if($stmt->num_rows == 1) {
        $stmt->bind_result($id_of_user_who_posted);
        $stmt->fetch();
        if($id_of_user_who_posted != $user_id) {
            // If this user is not the author
            echo "You do not have right to delete this story, since you are not the author of this story.";
            exit;
        }
    } else {
        // story does not exist
        echo "The story does not exist.";
        exit;
    }
}

// delete the story
$sql = "DELETE FROM stories WHERE story_id = ?";
if($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $story_id);
    if($stmt->execute()) {
        // redirect to index
        header("location: index.php");
        exit();
    } else {
        echo "Delete story failed. Please try again later.";
    }
}

$stmt->close();
$conn->close();
?>

