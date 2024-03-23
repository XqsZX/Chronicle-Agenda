<?php
session_start();

// check user login or not
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.html");
    exit;
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Publish a New Story</title>
</head>
<body>
    <h2>Publish a New Story</h2>
    <form action="submit_story.php" method="post">
        <div>
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" required>
        </div>
        <div>
            <label for="body">Story:</label>
            <textarea name="body" id="body" required></textarea>
        </div>
        <div>
            <label for="link">Link (optional):</label>
            <input type="text" name="link" id="link">
        </div>
        <button type="submit">Publish</button>
	<!-- send CSRF token -->
	<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" />
    </form>
    <a href="index.php">Back to Home</a>
</body>
</html>

