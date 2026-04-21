<?php
require_once 'database/config.php';

if (!isLoggedIn()) {
    header("Location: signin.php");
    exit;
}

$uid = (int)$_SESSION['user_id'];
$stmt = $conn->prepare("
    SELECT m.* FROM media m
    INNER JOIN favorites f ON f.media_id = m.id
    WHERE f.user_id = ?
    ORDER BY f.added_at DESC
");
$stmt->bind_param("i", $uid);
$stmt->execute();
$favs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My List — Streamy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="streamy.css">
    <style>
        .page-header {
            padding: 48px 48px 0;
        }
        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 40px;
            font-weight: 900;
            color: var(--white);
            margin-bottom: 6px;
        }
        .page-subtitle {
            font-size: 14px;
            color: var(--muted);
            border-left: 3px solid var(--red);
            padding-left: 12px;
        }
        .empty-fav {
            text-align: center;
            padding: 100px 20px;
            color: var(--muted);
        }
        .empty-fav i {
            font-size: 48px;
            color: var(--red);
            opacity: 0.3;
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .page-header { padding: 28px 16px 0; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="browse.php" class="logo">Stream<span>y</span></a>
    <ul class="nav-links">
        <li><a href="browse.php">Browse</a></li>
        <li><a href="favorites.php" class="active">My List</a></li>
        <li><a href="watchlater.php">Watch Later</a></li>
        <li><a href="history.php">History</a></li>
    </ul>
    <div class="nav-actions">
        <div class="nav-user">
            <div class="nav-avatar"><?= strtoupper(substr($_SESSION['username'], 0, 1)) ?></div>
            <span><?= htmlspecialchars($_SESSION['username']) ?></span>
        </div>
        <a href="database/logout.php" class="btn btn-outline" style="font-size:12px;padding:7px 14px;">
            <i class="fa-solid fa-sign-out-alt"></i> Logout
        </a>
    </div>
</nav>

<div class="page-header">
    <h1 class="page-title">My List</h1>
    <p class="page-subtitle"><?= count($favs) ?> saved title<?= count($favs) !== 1 ? 's' : '' ?></p>
</div>

<?php if (empty($favs)): ?>
<div class="empty-fav">
    <i class="fa-solid fa-heart"></i>
    <h3>Nothing saved yet</h3>
    <p>Browse titles and hit the heart icon to save them here.</p>
    <a href="browse.php" class="btn btn-primary" style="margin-top:24px;display:inline-flex;">
        <i class="fa-solid fa-film"></i> Browse Catalog
    </a>
</div>
<?php else: ?>
<div class="section">
    <div class="grid">
        <?php foreach ($favs as $m): ?>
        <div class="card" onclick="window.location.href='movie.php?id=<?= $m['id'] ?>'">
            <img class="card-poster" src="<?= htmlspecialchars($m['image_url']) ?>" alt="">
            <div class="card-body">
                <div class="card-title"><?= htmlspecialchars($m['title']) ?></div>
                <div class="card-meta">
                    <span><?= $m['release_year'] ?></span>
                    <span class="card-rating">&#9733; <?= number_format($m['rating'], 1) ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<footer class="footer">
    <div class="footer-logo">Stream<span>y</span></div>
    <span>© 2025 Streamy</span>
</footer>

</body>
</html>