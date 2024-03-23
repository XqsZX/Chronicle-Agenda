<?php
session_start();
require 'config.php'; // connect to mysql

// generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// check POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    
    // check username exist or not
    $sql = "SELECT user_id FROM users WHERE username = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);
        if($stmt->execute()) {
            $stmt->store_result();
            if($stmt->num_rows == 1) {
                echo "This username is already taken.";
            } else {
                // insert new user
                $sql = "INSERT INTO users (username, password_hash) VALUES (?, ?)";
                if ($stmt = $conn->prepare($sql)) {
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT); // password hash
                    $stmt->bind_param("ss", $username, $passwordHash);
                    if ($stmt->execute()) {
                        header("location: login.html"); // redirect to login
                    } else {
                        echo "Something went wrong. Please try again later.";
                    }
                }
            }
        }
        $stmt->close();
    }
    $conn->close();
}
?>

