<?php
require_once 'config.php';
header('Content-Type: application/json');
if (!isLoggedIn()) { echo json_encode(['status'=>'error']); exit; }
$data = json_decode(file_get_contents('php://input'), true);
$media_id = (int)($data['media_id'] ?? 0);
if ($media_id) echo json_encode(toggleFavorite($_SESSION['user_id'], $media_id));
else echo json_encode(['status'=>'error','message'=>'Invalid ID']);
