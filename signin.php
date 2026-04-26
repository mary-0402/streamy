<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign In — Streamy</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="streamy.css">
</head>
<body>
<?php
require_once 'database/config.php';
if (isLoggedIn()) { header("Location: browse.php"); exit; }
$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<div class="auth-page">
  <div class="auth-box">
    <div class="auth-logo">Stream<span>y</span></div>
    <h2 class="auth-title">Welcome back</h2>
    <p class="auth-subtitle">Sign in to continue watching</p>
    <?php if ($error): ?>
      <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" action="database/signin.php">
      <div class="form-group">
        <label class="form-label">Email</label>
        <div class="input-wrap">
          <i class="fa-regular fa-envelope"></i>
          <input class="form-input" type="email" name="email" placeholder="your@email.com" required>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <div class="input-wrap">
          <i class="fa-solid fa-lock"></i>
          <input class="form-input" type="password" name="password" placeholder="••••••••" required>
        </div>
      </div>
      <a href="#" class="forgot-link">Forgot password?</a>
      <button type="submit" class="form-submit"><i class="fa-solid fa-arrow-right-to-bracket"></i> Sign in</button>
    </form>
    <div class="form-divider">or</div>
    <div class="form-footer">Don't have an account? <a href="signup.php">Sign up free</a></div>
  </div>
</div>
</body>
</html>
