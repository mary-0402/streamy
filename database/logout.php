<?php
// ─────────────────────────────────────────────
//  logout.php — Destroy session and redirect
// ─────────────────────────────────────────────

session_start();
session_unset();
session_destroy();

header("Location: ../index.php");
exit;
?>
