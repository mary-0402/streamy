<?php
// database/remove_history_item.php
require_once 'config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$history_id = $data['history_id'] ?? 0;

if (!$history_id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid history ID']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Delete specific history item
$stmt = $conn->prepare("DELETE FROM watch_history WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $history_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Item removed']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to remove item']);
}

$stmt->close();
$conn->close();
?>