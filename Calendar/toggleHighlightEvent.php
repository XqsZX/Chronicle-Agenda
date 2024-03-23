<?php
session_start();
require 'config.php';

header('Content-Type: application/json');

// check login or not
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && isset($_POST['event_id'], $_POST['is_highlighted'])) {
    $event_id = $_POST['event_id'];
    $is_highlighted = $_POST['is_highlighted'] === 'true' ? 1 : 0; // change it to boolean
    $user_id = $_SESSION['user_id']; // get user_id from session

    // update the highlight
    $stmt = $conn->prepare("UPDATE events SET is_highlighted = ? WHERE event_id = ? AND user_id = ?");
    $stmt->bind_param("iii", $is_highlighted, $event_id, $user_id);

    if($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Event highlight toggled successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error toggling event highlight.']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in or missing data.']);
}

$conn->close();
?>

