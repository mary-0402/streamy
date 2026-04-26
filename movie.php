<?php
require_once 'database/config.php';
if (!isLoggedIn()) { header("Location: signin.php"); exit; }

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM media WHERE id=?");
$stmt->bind_param("i",$id); $stmt->execute();
$m = $stmt->get_result()->fetch_assoc();
if (!$m) { header("Location: browse.php"); exit; }

$uid   = (int)$_SESSION['user_id'];
$isFav = $conn->prepare("SELECT id FROM favorites WHERE user_id=? AND media_id=?");
$isFav->bind_param("ii",$uid,$id); $isFav->execute();
$isFav = $isFav->get_result()->num_rows > 0;
$isWL  = $conn->prepare("SELECT id FROM watch_later WHERE user_id=? AND media_id=?");
$isWL->bind_param("ii",$uid,$id); $isWL->execute();
$isWL  = $isWL->get_result()->num_rows > 0;
$isS   = $m['type']==='series';
$stars = round($m['rating']/2);

$episodesBySeason = [];
if ($isS) {
    $ep = $conn->prepare("SELECT * FROM episodes WHERE media_id=? ORDER BY season_number,episode_number");
    $ep->bind_param("i",$id); $ep->execute();
    foreach ($ep->get_result()->fetch_all(MYSQLI_ASSOC) as $e)
        $episodesBySeason[$e['season_number']][] = $e;
}
addToWatchHistory($uid, $id);
?>