<?php
// signup.php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: browse.php");
    exit;
}
$errors = $_SESSION['signup_errors'] ?? [];
unset($_SESSION['signup_errors']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Streamy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="streamy.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-box">
        <div class="auth-logo">Stream<span>y</span></div>
        <h2 class="auth-title">Inscription</h2>
        
        <?php foreach ($errors as $e): ?>
            <div class="alert alert-error"><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
        
        <form action="database/signup.php" method="POST">
            <div class="form-group">
                <label class="form-label">Nom d'utilisateur</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="username" class="form-input" required>
                </div>
            </div>
            
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
            
            <div class="form-group">
                <label class="form-label">Confirmer mot de passe</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="confirm" class="form-input" required>
                </div>
            </div>
            
            <button type="submit" class="form-submit">S'inscrire</button>
        </form>
        
        <div class="form-footer">
            Déjà inscrit ? <a href="signin.php">Connectez-vous</a>
        </div>
    </div>
</div>
</body>
</html>