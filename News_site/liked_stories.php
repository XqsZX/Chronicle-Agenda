<?php
session_start();
require 'config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// If user is not login, redirect to login page 
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Get user's liked story
$sql = "SELECT stories.story_id, stories.title 
        FROM stories 
        JOIN article_votes ON stories.story_id = article_votes.story_id 
        WHERE article_votes.user_id = ? AND article_votes.vote_type = 'like'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Liked Stories</title>
</head>
<body>
    <h1>Liked Stories</h1>
    <ul>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <li>
                    <a href="story_detail.php?id=<?php echo $row['story_id']; ?>">
                        <?php echo htmlspecialchars($row['title']); ?>
                    </a>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <p>You have not liked any stories yet.</p>
        <?php endif; ?>
    </ul>
    <a href="index.php" style="display:block; margin-top:20px;">Back to Home</a>
</body>
</html>

