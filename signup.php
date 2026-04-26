<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign Up — Streamy</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="streamy.css">
</head>
<body>
<?php
require_once 'database/config.php';
if (isLoggedIn()) { header("Location: browse.php"); exit; }
$errors = $_SESSION['signup_errors'] ?? [];
unset($_SESSION['signup_errors']);
?>
<div class="auth-page">
  <div class="auth-box">
    <div class="auth-logo">Stream<span>y</span></div>
    <h2 class="auth-title">Create your account</h2>
    <p class="auth-subtitle">Free forever. No credit card needed.</p>
    <?php foreach ($errors as $e): ?>
      <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>
    <form method="POST" action="database/signup.php">
      <div class="form-group">
        <label class="form-label">Username</label>
        <div class="input-wrap">
          <i class="fa-regular fa-user"></i>
          <input class="form-input" type="text" name="username" placeholder="yourname" required>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Email</label>
        <div class="input-wrap">
          <i class="fa-regular fa-envelope"></i>
          <input class="form-input" type="email" name="email" placeholder="your@email.com" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Password</label>
          <div class="input-wrap">
            <i class="fa-solid fa-lock"></i>
            <input class="form-input" type="password" name="password" placeholder="Min 6 chars" required>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Confirm</label>
          <div class="input-wrap">
            <i class="fa-solid fa-lock"></i>
            <input class="form-input" type="password" name="confirm" placeholder="Repeat" required>
          </div>
        </div>
      </div>
      <button type="submit" class="form-submit"><i class="fa-solid fa-user-plus"></i> Create account</button>
    </form>
    <div class="form-divider">or</div>
    <div class="form-footer">Already have an account? <a href="signin.php">Sign in</a></div>
  </div>
</div>
</body>
</html>
