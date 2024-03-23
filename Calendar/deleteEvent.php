<?php
session_start();
require 'config.php';

header('Content-Type: application/json');

// check user is login or not
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['user_id']; // get user_id from session

    // delete event from database
    $query = $conn->prepare("DELETE FROM events WHERE event_id = ? AND user_id = ?");
    $query->bind_param("ii", $event_id, $user_id);
    
    if($query->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Event deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting event']);
    }

    $query->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in or missing event ID']);
}

$conn->close();
?>

