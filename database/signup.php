<?php
// database/signup.php
require_once 'config.php';

if (isLoggedIn()) {
    header("Location: ../browse.php");
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(htmlspecialchars($_POST['username'] ?? ''));
    $email = trim(strtolower($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    // Validation
    if (empty($username)) {
        $errors[] = "Username is required";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match";
    }

    // Check if email exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email already registered";
        }
        $stmt->close();
    }

    // Insert user
    if (empty($errors)) {
        $hashed = hash('sha256', $password);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed);
        
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            
            header("Location: ../browse.php");
            exit;
        } else {
            $errors[] = "Registration failed";
        }
        $stmt->close();
    }

    if (!empty($errors)) {
        $_SESSION['signup_errors'] = $errors;
        header("Location: ../signup.php");
        exit;
    }
}
?>