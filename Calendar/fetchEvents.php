<?php
session_start();
require 'config.php';

header('Content-Type: application/json');

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    $user_id = $_SESSION['user_id']; 

    // prepare sql query
    $sql = "SELECT * FROM events WHERE user_id = ?";

    // query for data
    if($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        if($stmt->execute()) {
            $result = $stmt->get_result();
            $events = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['status' => 'success', 'events' => $events]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Could not retrieve events.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'SQL statement preparation failed.']);
    }
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
}
?>

