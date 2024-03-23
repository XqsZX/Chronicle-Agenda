<?php
session_start(); // session start
require 'config.php';

// Generate CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Check username and password
    $sql = "SELECT user_id, username, password_hash FROM users WHERE username = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($id, $username, $hashedPassword);
                if ($stmt->fetch()) {
                    if (password_verify($password, $hashedPassword)) {
                        // Password Correct
                        $_SESSION["loggedin"] = true;
                        $_SESSION["user_id"] = $id;
                        $_SESSION["username"] = $username;

                        // redirect to origin page if have, or redirect to index
                        if (!empty($_SESSION['redirect_after_login'])) {
                            $redirect_url = $_SESSION['redirect_after_login'];
                            unset($_SESSION['redirect_after_login']); // delete right after use
                            header('Location: ' . $redirect_url);
                        } else {
                            header("location: index.php"); // redirect to index page
                        }
                        exit;
                    } else {
                        // Invalid usename or password
                        echo "Invalid username or password.";
                    }
                }
            } else {
                echo "Invalid username or password.";
            }
        }
        $stmt->close();
    }
    $conn->close();
}
?>

