<?php
require_once 'database/config.php';
if (!isLoggedIn()) { header("Location: signin.php"); exit; }

$uid = (int)$_SESSION['user_id'];

$all      = $conn->query("SELECT * FROM media ORDER BY rating DESC")->fetch_all(MYSQLI_ASSOC);
$favIds   = array_column($conn->query("SELECT media_id FROM favorites WHERE user_id=$uid")->fetch_all(MYSQLI_ASSOC), 'media_id');
$wlIds    = array_column($conn->query("SELECT media_id FROM watch_later WHERE user_id=$uid")->fetch_all(MYSQLI_ASSOC), 'media_id');
$featured = $conn->query("SELECT * FROM media WHERE type='movie' ORDER BY rating DESC LIMIT 1")->fetch_assoc();

$genres = [
    ['key'=>'all',      'label'=>'All'],
    ['key'=>'action',   'label'=>'Action'],
    ['key'=>'drama',    'label'=>'Drama'],
    ['key'=>'scifi',    'label'=>'Sci-Fi'],
    ['key'=>'comedy',   'label'=>'Comedy'],
    ['key'=>'horror',   'label'=>'Horror'],
    ['key'=>'anime',    'label'=>'Anime'],
    ['key'=>'kids',     'label'=>'Kids'],
    ['key'=>'nostalgie','label'=>'Nostalgie'],
];
?>