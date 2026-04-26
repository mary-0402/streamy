<?php
require_once 'database/config.php';
if (!isLoggedIn()) { header("Location: signin.php"); exit; }

$uid  = (int)$_SESSION['user_id'];
$user = getUserData($uid);

$favs    = $conn->query("SELECT m.* FROM media m JOIN favorites f ON f.media_id=m.id WHERE f.user_id=$uid ORDER BY f.added_at DESC")->fetch_all(MYSQLI_ASSOC);
$wl      = $conn->query("SELECT m.* FROM media m JOIN watch_later wl ON wl.media_id=m.id WHERE wl.user_id=$uid ORDER BY wl.added_at DESC")->fetch_all(MYSQLI_ASSOC);
$history = getWatchHistory($uid, 30);
?>