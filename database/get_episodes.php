<?php
require_once '../config.php';
header('Content-Type: application/json');

$media_id = $_GET['media_id'] ?? 0;
$season = $_GET['season'] ?? 1;

if (!$media_id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid media ID']);
    exit;
}

$stmt = $conn->prepare("
    SELECT * FROM episodes 
    WHERE media_id = ? AND season_number = ? 
    ORDER BY episode_number ASC
");
$stmt->bind_param("ii", $media_id, $season);
$stmt->execute();
$episodes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode(['status' => 'success', 'episodes' => $episodes]);
?>