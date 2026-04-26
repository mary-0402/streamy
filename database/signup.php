<?php
require_once 'config.php';
if (isLoggedIn()) { header("Location: ../browse.php"); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(htmlspecialchars($_POST['username'] ?? ''));
    $email    = trim(strtolower($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';
    $errors   = [];
    if (empty($username)) $errors[] = "Username is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email address.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
    if ($password !== $confirm) $errors[] = "Passwords do not match.";
    if (empty($errors)) {
        $chk = $conn->prepare("SELECT id FROM users WHERE email=?");
        $chk->bind_param("s",$email); $chk->execute();
        if ($chk->get_result()->num_rows > 0) $errors[] = "Email already registered.";
    }
    if (empty($errors)) {
        $hashed = hash('sha256', $password);
        $stmt = $conn->prepare("INSERT INTO users (username,email,password) VALUES (?,?,?)");
        $stmt->bind_param("sss",$username,$email,$hashed);
        if ($stmt->execute()) {
            $_SESSION['user_id']  = $stmt->insert_id;
            $_SESSION['username'] = $username;
            $_SESSION['email']    = $email;
            header("Location: ../browse.php"); exit;
        } else { $errors[] = "Registration failed. Please try again."; }
    }
    $_SESSION['signup_errors'] = $errors;
    header("Location: ../signup.php"); exit;
}
