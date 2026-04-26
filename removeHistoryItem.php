<?php
require_once 'config.php';
header('Content-Type: application/json');
if (!isLoggedIn()) { echo json_encode(['status'=>'error']); exit; }
$data = json_decode(file_get_contents('php://input'), true);
$hid  = (int)($data['history_id'] ?? 0);
if ($hid) {
    $stmt = $conn->prepare("DELETE FROM watch_history WHERE id=? AND user_id=?");
    $stmt->bind_param("ii",$hid,$_SESSION['user_id']);
    echo $stmt->execute() ? json_encode(['status'=>'success']) : json_encode(['status'=>'error']);
} else echo json_encode(['status'=>'error']);
