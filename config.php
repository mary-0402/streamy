<?php
// database/config.php
session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'streamy3');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
$conn->set_charset("utf8mb4");

function isLoggedIn() { return isset($_SESSION['user_id']); }

function getUserData($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function addToWatchHistory($user_id, $media_id, $episode_id = null) {
    global $conn;
    // Avoid duplicate in same session (same media within 5 min)
    $stmt = $conn->prepare("SELECT id FROM watch_history WHERE user_id=? AND media_id=? AND watched_at > NOW() - INTERVAL 5 MINUTE");
    $stmt->bind_param("ii", $user_id, $media_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) return;
    $stmt = $conn->prepare("INSERT INTO watch_history (user_id, media_id, episode_id, watched_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iii", $user_id, $media_id, $episode_id);
    $stmt->execute();
}

function toggleFavorite($user_id, $media_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM favorites WHERE user_id=? AND media_id=?");
    $stmt->bind_param("ii", $user_id, $media_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $conn->prepare("DELETE FROM favorites WHERE user_id=? AND media_id=?")->bind_param("ii",$user_id,$media_id);
        $conn->prepare("DELETE FROM favorites WHERE user_id=? AND media_id=?")->execute();
        $d = $conn->prepare("DELETE FROM favorites WHERE user_id=? AND media_id=?");
        $d->bind_param("ii",$user_id,$media_id); $d->execute();
        return ['status'=>'removed'];
    }
    $i = $conn->prepare("INSERT INTO favorites (user_id,media_id) VALUES (?,?)");
    $i->bind_param("ii",$user_id,$media_id); $i->execute();
    return ['status'=>'added'];
}

function toggleWatchLater($user_id, $media_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM watch_later WHERE user_id=? AND media_id=?");
    $stmt->bind_param("ii",$user_id,$media_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $d = $conn->prepare("DELETE FROM watch_later WHERE user_id=? AND media_id=?");
        $d->bind_param("ii",$user_id,$media_id); $d->execute();
        return ['status'=>'removed'];
    }
    $i = $conn->prepare("INSERT INTO watch_later (user_id,media_id,added_at) VALUES (?,?,NOW())");
    $i->bind_param("ii",$user_id,$media_id); $i->execute();
    return ['status'=>'added'];
}

function getWatchHistory($user_id, $limit=50) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT wh.id, wh.watched_at, m.id as media_id, m.title, m.type, m.image_url,
               e.title as episode_title, e.season_number, e.episode_number
        FROM watch_history wh
        JOIN media m ON wh.media_id = m.id
        LEFT JOIN episodes e ON wh.episode_id = e.id
        WHERE wh.user_id = ?
        ORDER BY wh.watched_at DESC
        LIMIT ?
    ");
    $stmt->bind_param("ii",$user_id,$limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
