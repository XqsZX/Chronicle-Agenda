<?php
session_start();
require 'config.php';

// make sure user is login
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.html");
    exit;
}

// check events of users
$stmt = $conn->prepare("SELECT * FROM events WHERE user_id = ? ORDER BY event_date, event_time");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$events = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event List</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Event List</h1>
        <nav>
            <ul>
                <li><a href="logout.php">Logout</a></li>
                <li><a href="index.php">Calendar</a></li>
            </ul>
        </nav>
    </header>

    <section>
        <ul id="event-list">
            <?php foreach($events as $event): ?>
            <li style="color: <?= $event['is_highlighted'] ? 'red' : 'initial'; ?>">
                <?= htmlspecialchars($event['title']); ?> - <?= $event['event_date']; ?> <?= $event['event_time']; ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </section>

</body>
</html>

