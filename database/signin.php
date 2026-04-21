<?php
// database/signin.php
require_once 'config.php';

if (isLoggedIn()) {
    header("Location: ../browse.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim(strtolower($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $hashed = hash('sha256', $password);
        $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE email = ? AND password = ?");
        $stmt->bind_param("ss", $email, $hashed);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            
            // Update last login
            $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $updateStmt->bind_param("i", $user['id']);
            $updateStmt->execute();
            
            header("Location: ../browse.php");
            exit;
        } else {
            $_SESSION['login_error'] = "Email ou mot de passe incorrect";
            header("Location: ../signin.php");
            exit;
        }
        $stmt->close();
    } else {
        $_SESSION['login_error'] = "Veuillez remplir tous les champs";
        header("Location: ../signin.php");
        exit;
    }
}
?>