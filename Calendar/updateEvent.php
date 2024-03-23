<?php
session_start();
require 'config.php'; 

header('Content-Type: application/json');

// check user is login or not
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && isset($_POST['event_id'], $_POST['title'], $_POST['event_date'], $_POST['event_time'])) {
    $event_id = $_POST['event_id'];
    $title = $_POST['title'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $user_id = $_SESSION['user_id']; // get user_id from session

    // prepare sql update query
    $stmt = $conn->prepare("UPDATE events SET title = ?, event_date = ?, event_time = ? WHERE event_id = ? AND user_id = ?");
    $stmt->bind_param("sssii", $title, $event_date, $event_time, $event_id, $user_id);

    // try to update
    if($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Event updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error updating event.']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in or missing data.']);
}

$conn->close();
?>

