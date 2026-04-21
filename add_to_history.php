<?php
require_once '../config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['status' => 'error']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$media_id = $data['media_id'] ?? 0;
$episode_id = $data['episode_id'] ?? null;

if ($media_id) {
    addToWatchHistory($_SESSION['user_id'], $media_id, $episode_id);
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error']);
}
?>