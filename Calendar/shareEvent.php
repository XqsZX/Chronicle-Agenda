<?php
session_start();
require 'config.php';

header('Content-Type: application/json');

// check is login or not and the info
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && isset($_POST['title'], $_POST['event_date'], $_POST['event_time'], $_POST['shared_with'])) {
    $title = $_POST['title'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $shared_with = $_POST['shared_with']; // user_id of user being shared

    // make new event
    $stmt = $conn->prepare("INSERT INTO events (user_id, title, event_date, event_time) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $shared_with, $title, $event_date, $event_time);

    if($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Event shared successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error sharing event.']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in or missing data.']);
}

$conn->close();
?>

