<?php
session_start();
require 'config.php';

// check login or not
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$comment_id = $_GET['id'] ?? null;
$comment_content = '';
$story_id = 0; // initialize it as 0

// Get comment and its story id
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $comment_id) {
    $sql = "SELECT comment, user_id, story_id FROM comments WHERE comment_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $comment_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($comment_content, $user_id, $story_id);
            $stmt->fetch();

            // check the user is the commenter or not
            if ($user_id != $_SESSION['user_id']) {
                // if not
                header("location: index.php");
                exit;
            }
        } else {
            // comment not exist
            header("location: index.php");
            exit;
        }
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // check CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }

    $comment_id = $_POST['comment_id'];
    $comment_content = $_POST['comment'];
    $story_id = $_POST['story_id']; // Get story id

    // edit comment
    $sql = "UPDATE comments SET comment = ? WHERE comment_id = ? AND user_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sii", $comment_content, $comment_id, $_SESSION['user_id']);
        $stmt->execute();
        // redirect to story detail
        header("location: story_detail.php?id=" . $story_id);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Comment</title>
</head>
<body>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="comment_id" value="<?php echo $comment_id; ?>">
        <input type="hidden" name="story_id" value="<?php echo $story_id; ?>"> 
        <textarea name="comment" required><?php echo htmlspecialchars($comment_content); ?></textarea>
        <button type="submit">Update Comment</button>
    </form>
</body>
</html>

