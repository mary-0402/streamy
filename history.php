<?php
require_once 'database/config.php';

if (!isLoggedIn()) {
    header("Location: signin.php");
    exit;
}

$history = getWatchHistory($_SESSION['user_id'], 50);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watch History — Streamy</title>
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
            background: linear-gradient(135deg, var(--white), var(--red-light));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .page-subtitle {
            font-size: 16px;
            color: var(--muted);
            border-left: 3px solid var(--red);
            padding-left: 15px;
            display: inline-block;
            margin-top: 10px;
        }
        .stats-bar {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 30px auto;
            flex-wrap: wrap;
        }
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 15px 25px;
            text-align: center;
            min-width: 150px;
        }
        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: var(--red-light);
        }
        .stat-label {
            font-size: 12px;
            color: var(--muted);
            margin-top: 5px;
        }
        .history-list {
            max-width: 900px;
            margin: 0 auto;
        }
        .history-item {
            display: flex;
            gap: 20px;
            padding: 20px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        .history-item:hover {
            border-color: var(--red);
            transform: translateX(8px);
            background: var(--bg3);
        }
        .history-poster {
            width: 100px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .history-info {
            flex: 1;
        }
        .history-title {
            font-weight: 700;
            font-size: 18px;
            color: var(--white);
            margin-bottom: 8px;
        }
        .history-type {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .type-movie {
            background: var(--red);
            color: white;
        }
        .type-series {
            background: var(--gold);
            color: #111;
        }
        .history-meta {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 10px;
        }
        .history-meta i {
            margin-right: 5px;
            color: var(--red-light);
        }
        .history-date {
            font-size: 12px;
            color: var(--gold);
        }
        .remove-history-item {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(0,0,0,0.7);
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            opacity: 0;
        }
        .history-item:hover .remove-history-item {
            opacity: 1;
        }
        .remove-history-item:hover {
            background: var(--red);
            transform: scale(1.1);
        }
        .remove-history-item i {
            color: white;
            font-size: 12px;
        }
        .clear-history {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--red);
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 50px;
            cursor: pointer;
            z-index: 100;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(139,26,42,0.4);
        }
        .clear-history:hover {
            background: var(--red-light);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(139,26,42,0.5);
        }
        .empty-fav {
            text-align: center;
            padding: 100px 20px;
            color: var(--muted);
        }
        .empty-fav i {
            font-size: 80px;
            color: var(--red);
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
        .toast {
            position: fixed;
            bottom: 100px;
            right: 30px;
            background: var(--gold);
            color: #111;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            z-index: 1000;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @media (max-width: 768px) {
            .page-header {
                padding: 30px 20px 0;
            }
            .page-title {
                font-size: 36px;
            }
            .history-item {
                flex-direction: column;
                text-align: center;
            }
            .history-poster {
                width: 120px;
                height: 180px;
                margin: 0 auto;
            }
            .remove-history-item {
                opacity: 1;
                top: 10px;
                right: 10px;
            }
            .clear-history {
                bottom: 20px;
                right: 20px;
                padding: 10px 18px;
                font-size: 12px;
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
        <li><a href="watchlater.php">Watch Later</a></li>
        <li><a href="history.php" class="active">History</a></li>
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
        <i class="fa-regular fa-clock"></i> Watch History
    </h1>
    <p class="page-subtitle">Continue watching where you left off</p>
</div>

<?php if (!empty($history)): 
    $totalMovies = 0;
    $totalSeries = 0;
    foreach ($history as $item) {
        if ($item['type'] === 'movie') {
            $totalMovies++;
        } else {
            $totalSeries++;
        }
    }
?>
<div class="stats-bar">
    <div class="stat-card">
        <div class="stat-number"><?= count($history) ?></div>
        <div class="stat-label">Total Views</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $totalMovies ?></div>
        <div class="stat-label">Movies</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $totalSeries ?></div>
        <div class="stat-label">Series</div>
    </div>
</div>
<?php endif; ?>

<div class="section">
    <div class="history-list">
        <?php if (empty($history)): ?>
            <div class="empty-fav">
                <i class="fa-regular fa-eye"></i>
                <h3>No watch history yet</h3>
                <p>Start watching movies and series to see them here.</p>
                <a href="browse.php" class="btn btn-primary" style="margin-top:24px;display:inline-flex;padding:12px 30px;">
                    <i class="fa-solid fa-film"></i> Browse Catalog
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($history as $item): ?>
                <div class="history-item" data-history-id="<?= $item['id'] ?>" onclick="window.location.href='movie.php?id=<?= $item['media_id'] ?>'">
                    <button class="remove-history-item" onclick="removeHistoryItem(event, <?= $item['id'] ?>)">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                    
                    <img class="history-poster" src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                    
                    <div class="history-info">
                        <div class="history-title"><?= htmlspecialchars($item['title']) ?></div>
                        
                        <span class="history-type <?= $item['type'] === 'movie' ? 'type-movie' : 'type-series' ?>">
                            <i class="fa-<?= $item['type'] === 'movie' ? 'fa-film' : 'fa-tv' ?>"></i>
                            <?= ucfirst($item['type']) ?>
                        </span>
                        
                        <div class="history-meta">
                            <?php if ($item['episode_title']): ?>
                                <i class="fa-solid fa-list"></i> Season <?= $item['season_number'] ?>, Episode <?= $item['episode_number'] ?>
                                <br>
                                <i class="fa-solid fa-heading"></i> <?= htmlspecialchars($item['episode_title']) ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="history-date">
                            <i class="fa-regular fa-calendar"></i> 
                            <?= date('F j, Y \a\t g:i A', strtotime($item['watched_at'])) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($history)): ?>
<button class="clear-history" onclick="clearHistory()">
    <i class="fa-regular fa-trash-alt"></i> Clear All History
</button>
<?php endif; ?>

<footer class="footer">
    <div class="footer-logo">Stream<span>y</span></div>
    <div style="display: flex; gap: 20px; flex-wrap: wrap; justify-content: center;">
        <a href="#" style="color: var(--muted); text-decoration: none; font-size: 12px;">About</a>
        <a href="#" style="color: var(--muted); text-decoration: none; font-size: 12px;">Help Center</a>
        <a href="#" style="color: var(--muted); text-decoration: none; font-size: 12px;">Privacy</a>
        <a href="#" style="color: var(--muted); text-decoration: none; font-size: 12px;">Terms</a>
    </div>
    <span>© 2025 Streamy. All rights reserved.</span>
</footer>

<script>
function showToast(message, isError = false) {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.style.background = isError ? 'var(--red)' : 'var(--gold)';
    toast.style.color = isError ? 'white' : '#111';
    toast.innerHTML = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

function clearHistory() {
    if (confirm('⚠️ Are you sure you want to clear your entire watch history?\n\nThis action cannot be undone.')) {
        fetch('database/clear_history.php', { 
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showToast('✅ History cleared successfully!');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showToast('❌ Failed to clear history: ' + data.message, true);
            }
        })
        .catch(error => {
            showToast('❌ Error: ' + error, true);
        });
    }
}

function removeHistoryItem(event, historyId) {
    event.stopPropagation();
    
    if (confirm('Remove this item from your history?')) {
        fetch('database/remove_history_item.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ history_id: historyId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const item = event.target.closest('.history-item');
                item.style.transition = 'all 0.3s ease';
                item.style.opacity = '0';
                item.style.transform = 'translateX(-100%)';
                setTimeout(() => {
                    item.remove();
                    showToast('✅ Item removed from history');
                    
                    // Check if no items left
                    const remaining = document.querySelectorAll('.history-item').length;
                    if (remaining === 0) {
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    }
                }, 300);
            } else {
                showToast('❌ Failed to remove item', true);
            }
        })
        .catch(error => {
            showToast('❌ Error: ' + error, true);
        });
    }
}
</script>

</body>
</html>