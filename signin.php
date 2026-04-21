<?php
// signin.php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: browse.php");
    exit;
}
$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Streamy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="streamy.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-box">
        <div class="auth-logo">Stream<span>y</span></div>
        <h2 class="auth-title">Connexion</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form action="database/signin.php" method="POST">
            <div class="form-group">
                <label class="form-label">Email</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" name="email" class="form-input" required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Mot de passe</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" class="form-input" required>
                </div>
            </div>
            
            <button type="submit" class="form-submit">Se connecter</button>
        </form>
        
        <div class="form-footer">
            Pas encore de compte ? <a href="signup.php">Inscrivez-vous</a>
        </div>
    </div>
</div>
</body>
</html>