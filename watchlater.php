<?php
require_once 'database/config.php';

if (!isLoggedIn()) {
    header("Location: signin.php");
    exit;
}

$uid = (int)$_SESSION['user_id'];
$stmt = $conn->prepare("
    SELECT m.* FROM media m
    INNER JOIN watch_later wl ON wl.media_id = m.id
    WHERE wl.user_id = ?
    ORDER BY wl.added_at DESC
");
$stmt->bind_param("i", $uid);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watch Later — Streamy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="streamy.css">
    <style>
        .page-header {
            padding: 48px 48px 0;
            text-align: center;
        }
        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            font-weight: 900;
            color: var(--white);
            margin-bottom: 10px;
            background: linear-gradient(135deg, var(--white), var(--gold));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .page-subtitle {
            font-size: 16px;
            color: var(--muted);
            border-left: 3px solid var(--gold);
            padding-left: 15px;
            display: inline-block;
            margin-top: 10px;
        }
        .empty-fav {
            text-align: center;
            padding: 100px 20px;
            color: var(--muted);
        }
        .empty-fav i {
            font-size: 80px;
            color: var(--gold);
            opacity: 0.3;
            margin-bottom: 25px;
        }
        .empty-fav h3 {
            font-size: 28px;
            color: var(--white);
            margin-bottom: 15px;
        }
        .empty-fav p {
            font-size: 16px;
            margin-bottom: 30px;
        }
        .section {
            padding: 40px 48px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 25px;
        }
        .card {
            position: relative;
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-8px);
            border-color: var(--gold);
            box-shadow: 0 15px 30px rgba(0,0,0,0.3);
        }
        .card-poster {
            width: 100%;
            aspect-ratio: 2/3;
            object-fit: cover;
        }
        .card-body {
            padding: 15px;
        }
        .card-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--white);
            margin-bottom: 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: var(--muted);
        }
        .card-rating {
            color: var(--gold);
            font-weight: 600;
        }
        .remove-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0,0,0,0.7);
            border: none;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            z-index: 10;
        }
        .remove-btn:hover {
            background: var(--red);
            transform: scale(1.1);
        }
        .remove-btn i {
            color: white;
            font-size: 14px;
        }
        @media (max-width: 768px) {
            .page-header {
                padding: 30px 20px 0;
            }
            .page-title {
                font-size: 36px;
            }
            .section {
                padding: 30px 20px;
            }
            .grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 15px;
            }
            .card-title {
                font-size: 14px;
            }
        }
        @media (max-width: 480px) {
            .grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="browse.php" class="logo">Stream<span>y</span></a>
    <ul class="nav-links">
        <li><a href="browse.php">Browse</a></li>
        <li><a href="favorites.php">My List</a></li>
        <li><a href="watchlater.php" class="active">Watch Later</a></li>
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
    <h1 class="page-title">
        <i class="fa-regular fa-clock"></i> Watch Later
    </h1>
    <p class="page-subtitle">
        <?= count($items) ?> <?= count($items) !== 1 ? 'items' : 'item' ?> waiting for you
    </p>
</div>

<?php if (empty($items)): ?>
<div class="empty-fav">
    <i class="fa-regular fa-clock"></i>
    <h3>Your watch later list is empty</h3>
    <p>Save movies and series you want to watch by clicking the clock icon.</p>
    <a href="browse.php" class="btn btn-primary" style="padding: 12px 30px;">
        <i class="fa-solid fa-film"></i> Browse Catalog
    </a>
</div>
<?php else: ?>
<div class="section">
    <div class="grid">
        <?php foreach ($items as $m): 
            $isSeries = $m['type'] === 'series';
        ?>
        <div class="card" onclick="window.location.href='movie.php?id=<?= $m['id'] ?>'">
            <img class="card-poster" src="<?= htmlspecialchars($m['image_url']) ?>" alt="<?= htmlspecialchars($m['title']) ?>">
            
            <span class="card-badge <?= $isSeries ? 'badge-series' : 'badge-movie' ?>">
                <?= $isSeries ? 'Series' : 'Movie' ?>
            </span>
            
            <button class="remove-btn" onclick="removeFromWatchLater(event, <?= $m['id'] ?>, this)" title="Remove from watch later">
                <i class="fa-solid fa-check"></i>
            </button>
            
            <div class="card-body">
                <div class="card-title"><?= htmlspecialchars($m['title']) ?></div>
                <div class="card-meta">
                    <span><?= $m['release_year'] ?></span>
                    <span class="card-rating">
                        <i class="fa-solid fa-star" style="font-size: 10px;"></i> <?= number_format($m['rating'], 1) ?>
                    </span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<footer class="footer">
    <div class="footer-logo">Stream<span>y</span></div>
    <div style="display: flex; gap: 20px; flex-wrap: wrap; justify-content: center;">
        <a href="#" style="color: var(--muted); text-decoration: none; font-size: 12px;">About</a>
        <a href="#" style="color: var(--muted); text-decoration: none; font-size: 12px;">Help</a>
        <a href="#" style="color: var(--muted); text-decoration: none; font-size: 12px;">Privacy</a>
        <a href="#" style="color: var(--muted); text-decoration: none; font-size: 12px;">Terms</a>
    </div>
    <span>© 2025 Streamy. All rights reserved.</span>
</footer>

<script>
function removeFromWatchLater(event, mediaId, button) {
    event.stopPropagation();
    
    fetch('database/toggle_watchlater.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ media_id: mediaId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'removed') {
            // Remove the card with animation
            const card = button.closest('.card');
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '0';
            card.style.transform = 'scale(0.8)';
            setTimeout(() => {
                card.remove();
                
                // Update counter
                const remainingCards = document.querySelectorAll('.card').length;
                const subtitle = document.querySelector('.page-subtitle');
                
                if (remainingCards === 0) {
                    location.reload();
                } else {
                    subtitle.textContent = remainingCards + ' ' + (remainingCards !== 1 ? 'items' : 'item') + ' waiting for you';
                }
            }, 300);
        }
    });
}
</script>

</body>
</html>