<?php
session_start();

// check login or not
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.html");
    exit;
}

require_once "config.php";

// manage data
$title = $_POST["title"];
$body = $_POST["body"];
$link = $_POST["link"] ?? ''; 
$user_id = $_SESSION["user_id"];

// insert story into mysql
$sql = "INSERT INTO stories (user_id, title, body, link) VALUES (?, ?, ?, ?)";
if($stmt = $conn->prepare($sql)){
    $stmt->bind_param("isss", $user_id, $title, $body, $link);
    
    if($stmt->execute()){
        // submit story successfully, redirect to index
        header("location: index.php");
        exit();
    } else{
        echo "Something went wrong. Please try again later.";
    }
}

$stmt->close();
$conn->close();
?>

