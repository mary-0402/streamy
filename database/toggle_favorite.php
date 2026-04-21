<?php
// database/toggle_favorite.php
require_once 'config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$media_id = $data['media_id'] ?? 0;

if ($media_id) {
    $result = toggleFavorite($_SESSION['user_id'], $media_id);
    echo json_encode($result);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid media ID']);
}
?>