<?php
// database/config.php - Database configuration
session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'streamy');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to get user data
function getUserData($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

// Function to get user profiles
function getUserProfiles($user_id) {
    global $conn;
    // Check if user_profiles table exists
    $checkTable = $conn->query("SHOW TABLES LIKE 'user_profiles'");
    if ($checkTable->num_rows == 0) {
        return []; // Return empty array if table doesn't exist
    }
    
    $stmt = $conn->prepare("SELECT * FROM user_profiles WHERE user_id = ? ORDER BY created_at ASC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $profiles = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $profiles;
}

// Function to add to watch history
function addToWatchHistory($user_id, $media_id, $episode_id = null, $progress = 0) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO watch_history (user_id, media_id, episode_id, progress, watched_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiii", $user_id, $media_id, $episode_id, $progress);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// Function to toggle favorite
function toggleFavorite($user_id, $media_id) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND media_id = ?");
    $stmt->bind_param("ii", $user_id, $media_id);
    $stmt->execute();
    $exists = $stmt->get_result()->num_rows > 0;
    $stmt->close();
    
    if ($exists) {
        $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND media_id = ?");
        $stmt->bind_param("ii", $user_id, $media_id);
        $stmt->execute();
        $stmt->close();
        return ['status' => 'removed'];
    } else {
        $stmt = $conn->prepare("INSERT INTO favorites (user_id, media_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $media_id);
        $stmt->execute();
        $stmt->close();
        return ['status' => 'added'];
    }
}

// Function to toggle watch later
function toggleWatchLater($user_id, $media_id) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT id FROM watch_later WHERE user_id = ? AND media_id = ?");
    $stmt->bind_param("ii", $user_id, $media_id);
    $stmt->execute();
    $exists = $stmt->get_result()->num_rows > 0;
    $stmt->close();
    
    if ($exists) {
        $stmt = $conn->prepare("DELETE FROM watch_later WHERE user_id = ? AND media_id = ?");
        $stmt->bind_param("ii", $user_id, $media_id);
        $stmt->execute();
        $stmt->close();
        return ['status' => 'removed'];
    } else {
        $stmt = $conn->prepare("INSERT INTO watch_later (user_id, media_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $media_id);
        $stmt->execute();
        $stmt->close();
        return ['status' => 'added'];
    }
}

// Function to get watch history
function getWatchHistory($user_id, $limit = 20) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT wh.*, m.title, m.type, m.image_url, 
               e.title as episode_title, e.season_number, e.episode_number
        FROM watch_history wh
        JOIN media m ON wh.media_id = m.id
        LEFT JOIN episodes e ON wh.episode_id = e.id
        WHERE wh.user_id = ?
        ORDER BY wh.watched_at DESC
        LIMIT ?
    ");
    $stmt->bind_param("ii", $user_id, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $history = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $history;
}
?>