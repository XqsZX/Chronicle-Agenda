<?php
session_start();
require 'config.php'; // Replace with your actual path to the database configuration file

// Check if the user is logged in
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    // Check if the necessary POST variables are set
    if(isset($_POST['title'], $_POST['event_date'], $_POST['event_time'])) {
        $title = $_POST['title'];
        $event_date = $_POST['event_date'];
        $event_time = $_POST['event_time'];
        $user_id = $_SESSION['user_id']; // Your session variable for user ID

        // Insert the new event into the database
        $query = $conn->prepare("INSERT INTO events (user_id, title, event_date, event_time) VALUES (?, ?, ?, ?)");
        $query->bind_param("isss", $user_id, $title, $event_date, $event_time);

        if($query->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Event added successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error adding event']);
        }

        $query->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing data for event addition']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
}

$conn->close();
?>

