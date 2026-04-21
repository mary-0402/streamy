<?php
// database/clear_history.php
require_once 'config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Delete all watch history for this user
$stmt = $conn->prepare("DELETE FROM watch_history WHERE user_id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'History cleared']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to clear history']);
}

$stmt->close();
$conn->close();
?>