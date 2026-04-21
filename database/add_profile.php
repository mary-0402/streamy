<?php
// database/clear_history.php
require_once 'config.php';

if (isLoggedIn()) {
    $stmt = $conn->prepare("DELETE FROM watch_history WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
}

header("Location: ../history.php");
exit;
?>