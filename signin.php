<?php
require_once 'config.php';
if (isLoggedIn()) { header("Location: ../browse.php"); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim(strtolower($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    if ($email && $password) {
        $hashed = hash('sha256', $password);
        $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE email=? AND password=?");
        $stmt->bind_param("ss", $email, $hashed);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        if ($user) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email']    = $user['email'];
            $conn->query("UPDATE users SET last_login=NOW() WHERE id=".(int)$user['id']);
            header("Location: ../browse.php"); exit;
        }
    }
    $_SESSION['login_error'] = "Invalid email or password.";
    header("Location: ../signin.php"); exit;
}
