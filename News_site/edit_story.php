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

$story_id = $_GET['id'] ?? null;
$title = '';
$body = '';

// Get details of story
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $story_id) {
    $sql = "SELECT title, body, user_id FROM stories WHERE story_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $story_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($title, $body, $user_id);
            $stmt->fetch();

            // check if user is author
            if ($user_id != $_SESSION['user_id']) {
                echo "You do not have permission to edit this story.";
                exit;
            }
        } else {
            echo "Story not found.";
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

    $story_id = $_POST['story_id'];
    $title = $_POST['title'];
    $body = $_POST['body'];

    // edit story
    $sql = "UPDATE stories SET title = ?, body = ? WHERE story_id = ? AND user_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssii", $title, $body, $story_id, $_SESSION['user_id']);
        if ($stmt->execute()) {
            header("Location: story_detail.php?id=" . $story_id);
            exit;
        } else {
            echo "An error occurred. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Story</title>
</head>
<body>
    <h1>Edit Story</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $story_id); ?>" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="story_id" value="<?php echo $story_id; ?>">
        <div>
            <label>Title:</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
        </div>
        <div>
            <label>Body:</label>
            <textarea name="body" required><?php echo htmlspecialchars($body); ?></textarea>
        </div>
        <button type="submit">Update Story</button>
    </form>
</body>
</html>

