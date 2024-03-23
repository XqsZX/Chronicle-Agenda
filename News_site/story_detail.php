<?php
session_start();
require 'config.php';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Make sure story ID is provided
if (!isset($_GET['id'])) {
    echo "Error: story ID not provided.";
    exit;
}

$story_id = $_GET['id'];

// Get story details and author info
$storyQuery = "SELECT stories.story_id, stories.title, stories.body, stories.link, stories.user_id, users.username AS author
               FROM stories
               JOIN users ON stories.user_id = users.user_id
               WHERE stories.story_id = ?";
$storyStmt = $conn->prepare($storyQuery);
$storyStmt->bind_param("i", $story_id);
$storyStmt->execute();
$storyResult = $storyStmt->get_result();
$story = $storyResult->fetch_assoc();

if (!$story) {
    echo "Story not found.";
    exit;
}

// Get comment detail and commenter info
$commentsQuery = "SELECT comments.comment_id, comments.comment, comments.user_id, users.username AS commenter
                  FROM comments
                  JOIN users ON comments.user_id = users.user_id
                  WHERE comments.story_id = ?
                  ORDER BY comments.comment_id DESC";
$commentsStmt = $conn->prepare($commentsQuery);
$commentsStmt->bind_param("i", $story_id);
$commentsStmt->execute();
$commentsResult = $commentsStmt->get_result();

// Check like_count and dislike_count
$likesQuery = "SELECT COUNT(*) AS like_count FROM article_votes WHERE story_id = ? AND vote_type = 'like'";
$likesStmt = $conn->prepare($likesQuery);
$likesStmt->bind_param("i", $story_id);
$likesStmt->execute();
$likesResult = $likesStmt->get_result();
$likeCount = $likesResult->fetch_assoc()['like_count'];

$dislikesQuery = "SELECT COUNT(*) AS dislike_count FROM article_votes WHERE story_id = ? AND vote_type = 'dislike'";
$dislikesStmt = $conn->prepare($dislikesQuery);
$dislikesStmt->bind_param("i", $story_id);
$dislikesStmt->execute();
$dislikesResult = $dislikesStmt->get_result();
$dislikeCount = $dislikesResult->fetch_assoc()['dislike_count'];

$storyStmt->close();
$likesStmt->close();
$dislikesStmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($story['title']); ?></title>
</head>
<body>
    <h1><?php echo htmlspecialchars($story['title']); ?></h1>
    <p><strong>Author:</strong> <?php echo htmlspecialchars($story['author']); ?></p>
    <?php
    // If user is the author, show edit and delete links
    if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == $story['user_id']) {
        echo "<a href='edit_story.php?id=$story_id'>Edit Story</a> | ";
        echo "<a href='delete_story.php?id=$story_id&csrf_token=" . $_SESSION['csrf_token'] . "' onclick='return confirm(\"Are you sure to delete this story?\")'>Delete Story</a>";
    }
    ?>

    <!-- story detail -->
    <p><?php echo nl2br(htmlspecialchars($story['body'])); ?></p>
    <?php if (!empty($story['link'])): ?>
        <p><a href="<?php echo htmlspecialchars($story['link']); ?>" target="_blank">Read more</a></p>
    <?php endif; ?>

    <p>Likes: <?php echo $likeCount; ?> Dislikes: <?php echo $dislikeCount; ?></p>

    <!-- like and dislike button -->
    <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
        <form action="vote.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="story_id" value="<?php echo $story_id; ?>">
            <input type="hidden" name="vote_type" value="like">
            <button type="submit">Like</button>
        </form>
        <form action="vote.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="story_id" value="<?php echo $story_id; ?>">
            <input type="hidden" name="vote_type" value="dislike">
            <button type="submit">Dislike</button>
        </form>
    <?php endif; ?>

    <!-- Submit comment -->
    <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
        <h3>Add a Comment</h3>
        <form action="submit_comment.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="story_id" value="<?php echo $story_id; ?>">
            <textarea name="comment" required></textarea>
            <button type="submit">Submit Comment</button>
        </form>
    <?php endif; ?>

    <!-- Show comments -->
    <h2>Comments</h2>
    <?php while ($comment = $commentsResult->fetch_assoc()): ?>
        <div>
            <p><strong><?php echo htmlspecialchars($comment['commenter']); ?></strong>: <?php echo htmlspecialchars($comment['comment']); ?></p>
            <?php
            // If user is the commenter, show edit and delete links
            if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == $comment['user_id']) {
                echo "<a href='edit_comment.php?id=" . $comment['comment_id'] . "'>Edit</a> | ";
                echo "<a href='delete_comment.php?id=" . $comment['comment_id'] . "&csrf_token=" . $_SESSION['csrf_token'] . "' onclick='return confirm(\"Are you sure to delete this comment?\")'>Delete</a>";
            }
            ?>
        </div>
    <?php endwhile; ?>

    <!-- Link to index -->
    <a href="index.php" style="display:block; margin-top:20px;">Back to Home</a>
</body>
</html>

