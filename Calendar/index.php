<?php
session_start();
require 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Calendar</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <?php
        if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
            echo "<h1>Welcome, " . htmlspecialchars($_SESSION["username"]) . ". Please plan your schedule!</h1>";
            echo "<p>uid: <span class='user-id'>" . htmlspecialchars($_SESSION["user_id"]) . "</span></p>";
        } else {
            echo "<h1>Welcome, please login to plan your schedule.</h1>";
        }
        ?>
    </header>
    <nav>
        <ul>
            <?php
            if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
                echo '<li><a href="logout.php">Logout</a></li>';
                echo '<li><a href="eventList.php">List</a></li>';
            } else {
                echo '<li><a href="login.html">Login</a></li>';
                echo '<li><a href="register.html">Register</a></li>';
            }
            ?>
        </ul>
    </nav>

    <div id="calendar-header">
        <h2 id="month-year-display"></h2>
    </div>
    <div id="calendar-container">
        <button id="prev-month">Previous Month</button>
        <button id="next-month">Next Month</button>
        <div id="calendar">
            <!-- the content of calendar will generate by js -->
        </div>
    </div>
    <script src="calendar.js"></script>

    <div id="addEventModal" style="display:none;">
        <input type="text" id="eventTitle" placeholder="Event Title">
        <input type="date" id="eventDate" placeholder="Event Date">
        <input type="time" id="eventTime" placeholder="Event Time">
        <button onclick="saveEvent()">Save Event</button>
        <button onclick="closeModal()">Cancel</button>
    </div>

    <!-- edit event modal -->
    <div id="editEventModal" style="display:none;">
        <input type="text" id="editEventTitle" placeholder="Event Title">
        <input type="date" id="editEventDate" placeholder="Event Date">
        <input type="time" id="editEventTime" placeholder="Event Time">
        <button onclick="updateEvent()">Update Event</button>
        <button onclick="closeEditModal()">Cancel</button>
    </div>

    <div id="add-event-container">
        <button id="add-event">Add New Event</button>
    </div>

    <script src="calendar.js"></script>

</body>
</html>

