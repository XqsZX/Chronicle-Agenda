<?php
session_start();
require 'config.php';

// Get all stories and sort by like_count
$sql = "SELECT stories.story_id, stories.title,
               (SELECT COUNT(*) FROM article_votes WHERE article_votes.story_id = stories.story_id AND article_votes.vote_type = 'like') AS like_count
        FROM stories
        GROUP BY stories.story_id
        ORDER BY like_count DESC, stories.story_id DESC";
$stories_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>News Feed</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <?php
        if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
            echo "<h1>Welcome to Our News Site, " . htmlspecialchars($_SESSION["username"]) . "!</h1>";
        } else {
            echo "<h1>Welcome to Our News Site</h1>";
        }
        ?>
    </header>
    <nav>
        <ul>
            <?php
            if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
                echo '<li><a href="logout.php">Logout</a></li>';
                echo '<li><a href="new_story.php">Publish a New Story</a></li>';
                echo '<li><a href="liked_stories.php">Liked Stories</a></li>';
                echo '<li><a href="disliked_stories.php">Disliked Stories</a></li>';
            } else {
                echo '<li><a href="login.html">Login</a></li>';
                echo '<li><a href="register.html">Register</a></li>';
            }
            ?>
        </ul>
    </nav>

    <main>
        <?php if ($stories_result->num_rows > 0): ?>
            <ul>
                <?php while($row = $stories_result->fetch_assoc()): ?>
                    <li>
                        <h2><a href="story_detail.php?id=<?php echo $row['story_id']; ?>">
                            <?php echo htmlspecialchars($row['title']); ?>
                        </a></h2>
                        <p>Likes: <?php echo $row['like_count']; ?></p>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No stories found.</p>
        <?php endif; ?>
    </main>
</body>
</html>

