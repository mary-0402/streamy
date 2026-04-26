<?php
require_once 'config.php';
header('Content-Type: application/json');
if (!isLoggedIn()) { echo json_encode(['status'=>'error']); exit; }
$stmt = $conn->prepare("DELETE FROM watch_history WHERE user_id=?");
$stmt->bind_param("i",$_SESSION['user_id']);
echo $stmt->execute() ? json_encode(['status'=>'success']) : json_encode(['status'=>'error']);
