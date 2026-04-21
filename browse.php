<?php
require_once 'database/config.php';

if (!isLoggedIn()) {
    header("Location: signin.php");
    exit;
}

// Get all media
$result = $conn->query("SELECT * FROM media ORDER BY rating DESC");
$all = $result->fetch_all(MYSQLI_ASSOC);

// Get user's favorites
$uid = (int)$_SESSION['user_id'];
$fq = $conn->query("SELECT media_id FROM favorites WHERE user_id = $uid");
$favIds = [];
while ($r = $fq->fetch_assoc()) {
    $favIds[] = $r['media_id'];
}

// Get watch later
$wl = $conn->query("SELECT media_id FROM watch_later WHERE user_id = $uid");
$watchLaterIds = [];
while ($r = $wl->fetch_assoc()) {
    $watchLaterIds[] = $r['media_id'];
}

// Get categories with icons
$categories = [
    'general' => ['name' => 'All', 'icon' => 'fa-film', 'color' => '#8B1A2A'],
    'anime' => ['name' => 'Anime', 'icon' => 'fa-dragon', 'color' => '#9b59b6'],
    'kids' => ['name' => 'Kids', 'icon' => 'fa-child', 'color' => '#2ecc71'],
    'horror' => ['name' => 'Horror', 'icon' => 'fa-ghost', 'color' => '#e74c3c'],
    'comedy' => ['name' => 'Comedy', 'icon' => 'fa-laugh', 'color' => '#f39c12']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse — Streamy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="streamy.css">
    <style>
        /* Hero Banner */
        .hero-banner {
            background: linear-gradient(135deg, var(--bg) 0%, var(--bg2) 100%);
            padding: 60px 48px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }
        .hero-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(139,26,42,0.1) 0%, transparent 70%);
            pointer-events: none;
        }
        .hero-content {
            position: relative;
            z-index: 1;
        }
        .hero-greeting {
            font-size: 14px;
            color: var(--red-light);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            font-weight: 900;
            color: var(--white);
            margin-bottom: 15px;
        }
        .hero-title span {
            color: var(--red-light);
        }
        .hero-text {
            font-size: 16px;
            color: var(--muted);
            max-width: 500px;
        }
        
        /* Stats Bar */
        .stats-bar {
            display: flex;
            gap: 30px;
            padding: 0 48px 30px;
            flex-wrap: wrap;
        }
        .stat-item {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--surface);
            padding: 10px 20px;
            border-radius: 12px;
            border: 1px solid var(--border);
        }
        .stat-icon {
            width: 40px;
            height: 40px;
            background: rgba(139,26,42,0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--red-light);
            font-size: 18px;
        }
        .stat-info h4 {
            font-size: 20px;
            font-weight: 700;
            color: var(--white);
        }
        .stat-info p {
            font-size: 11px;
            color: var(--muted);
        }
        
        /* Search Bar Improved */
        .search-wrapper {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 50px;
            padding: 5px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s;
        }
        .search-wrapper:focus-within {
            border-color: var(--red);
            box-shadow: 0 0 0 2px rgba(139,26,42,0.2);
        }
        .search-wrapper i {
            color: var(--muted);
            font-size: 16px;
        }
        .search-wrapper input {
            background: none;
            border: none;
            outline: none;
            color: var(--text);
            font-size: 14px;
            padding: 12px 0;
            width: 250px;
        }
        .search-wrapper input::placeholder {
            color: var(--muted);
        }
        
        /* Filters Improved */
        .filters {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }
        .filter-btn {
            padding: 8px 20px;
            border-radius: 30px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--muted);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .filter-btn i {
            font-size: 12px;
        }
        .filter-btn:hover {
            border-color: var(--red);
            color: var(--red-light);
            transform: translateY(-2px);
        }
        .filter-btn.active {
            background: var(--red);
            border-color: var(--red);
            color: white;
        }
        
        /* Cards Improved */
        .card {
            position: relative;
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card:hover {
            transform: translateY(-8px);
            border-color: var(--red);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }
        .card-poster {
            width: 100%;
            aspect-ratio: 2/3;
            object-fit: cover;
            transition: transform 0.3s;
        }
        .card:hover .card-poster {
            transform: scale(1.05);
        }
        .card-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            z-index: 2;
        }
        .badge-movie {
            background: var(--red);
            color: white;
        }
        .badge-series {
            background: var(--gold);
            color: #111;
        }
        .card-fav, .card-wl {
            position: absolute;
            width: 34px;
            height: 34px;
            background: rgba(0,0,0,0.7);
            border: 1px solid var(--border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: var(--muted);
            transition: all 0.2s;
            backdrop-filter: blur(4px);
            cursor: pointer;
            z-index: 2;
        }
        .card-fav {
            top: 10px;
            right: 10px;
        }
        .card-wl {
            top: 55px;
            right: 10px;
        }
        .card-fav:hover, .card-wl:hover {
            transform: scale(1.1);
        }
        .card-fav.active, .card-fav:hover {
            background: var(--red);
            border-color: var(--red);
            color: white;
        }
        .card-wl.active, .card-wl:hover {
            background: var(--gold);
            border-color: var(--gold);
            color: white;
        }
        .card-body {
            padding: 14px;
        }
        .card-title {
            font-size: 15px;
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
            font-size: 12px;
            color: var(--muted);
        }
        .card-rating {
            color: var(--gold);
            font-weight: 600;
        }
        .card-eps {
            font-size: 10px;
            background: rgba(201,168,76,0.15);
            color: var(--gold);
            padding: 2px 7px;
            border-radius: 4px;
        }
        
        /* Grid */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 25px;
        }
        
        /* Section */
        .section {
            padding: 0 48px 60px;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .hero-banner { padding: 40px 30px; }
            .stats-bar { padding: 0 30px 20px; }
            .section { padding: 0 30px 40px; }
            .grid { gap: 20px; }
        }
        @media (max-width: 768px) {
            .hero-banner { padding: 30px 20px; }
            .hero-title { font-size: 32px; }
            .stats-bar { padding: 0 20px 20px; gap: 15px; }
            .stat-item { padding: 8px 15px; }
            .stat-icon { width: 35px; height: 35px; }
            .stat-info h4 { font-size: 16px; }
            .section { padding: 0 20px 30px; }
            .grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; }
            .catalog-header { flex-direction: column; align-items: flex-start; }
            .search-wrapper input { width: 180px; }
        }
        @media (max-width: 480px) {
            .grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .card-title { font-size: 13px; }
            .card-fav, .card-wl { width: 28px; height: 28px; font-size: 11px; }
            .card-wl { top: 48px; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="browse.php" class="logo">Stream<span>y</span></a>
    <ul class="nav-links">
        <li><a href="browse.php" class="active">Browse</a></li>
        <li><a href="favorites.php">My List</a></li>
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

<!-- Hero Banner -->
<div class="hero-banner">
    <div class="hero-content">
        <div class="hero-greeting">
            <i class="fa-solid fa-fire"></i> WELCOME BACK
        </div>
        <h1 class="hero-title">
            Ready to watch<br>
            something <span>amazing</span>?
        </h1>
        <p class="hero-text">
            Discover the best movies and series, curated just for you.
        </p>
    </div>
</div>

<!-- Stats Bar -->
<div class="stats-bar">
    <div class="stat-item">
        <div class="stat-icon">
            <i class="fa-solid fa-film"></i>
        </div>
        <div class="stat-info">
            <h4><?= count($all) ?></h4>
            <p>Total Titles</p>
        </div>
    </div>
    <div class="stat-item">
        <div class="stat-icon">
            <i class="fa-solid fa-heart"></i>
        </div>
        <div class="stat-info">
            <h4><?= count($favIds) ?></h4>
            <p>In Your List</p>
        </div>
    </div>
    <div class="stat-item">
        <div class="stat-icon">
            <i class="fa-solid fa-clock"></i>
        </div>
        <div class="stat-info">
            <h4><?= count($watchLaterIds) ?></h4>
            <p>Watch Later</p>
        </div>
    </div>
</div>

<!-- Search and Filters -->
<div class="catalog-header">
    <div>
        <h1 class="catalog-title">Browse Catalog</h1>
    </div>
    <div style="display:flex;align-items:center;gap:15px;flex-wrap:wrap;">
        <span class="count-badge" id="count-badge"><?= count($all) ?> titles</span>
        <div class="search-wrapper">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="search" placeholder="Search by title...">
        </div>
    </div>
</div>

<div class="filters-wrap">
    <div class="filters" id="filters">
        <button class="filter-btn active" data-filter="all">
            <i class="fa-solid fa-border-all"></i> All
        </button>
        <button class="filter-btn" data-filter="movie">
            <i class="fa-solid fa-film"></i> Movies
        </button>
        <button class="filter-btn" data-filter="series">
            <i class="fa-solid fa-tv"></i> Series
        </button>
        <?php foreach ($categories as $key => $cat): ?>
            <button class="filter-btn" data-category="<?= $key ?>">
                <i class="fa-solid <?= $cat['icon'] ?>"></i> <?= $cat['name'] ?>
            </button>
        <?php endforeach; ?>
    </div>
</div>

<!-- Catalog Grid -->
<div class="section">
    <div class="grid" id="catalog">
        <?php foreach ($all as $m):
            $isFav = in_array($m['id'], $favIds);
            $isWL = in_array($m['id'], $watchLaterIds);
            $isS = $m['type'] === 'series';
        ?>
        <div class="card"
             data-type="<?= $m['type'] ?>"
             data-category="<?= $m['category'] ?>"
             data-title="<?= strtolower(htmlspecialchars($m['title'])) ?>"
             onclick="window.location.href='movie.php?id=<?= $m['id'] ?>'">

            <img class="card-poster"
                 src="<?= htmlspecialchars($m['image_url']) ?>"
                 alt="<?= htmlspecialchars($m['title']) ?>"
                 loading="lazy">

            <span class="card-badge <?= $isS ? 'badge-series' : 'badge-movie' ?>">
                <?= $isS ? 'Series' : 'Movie' ?>
            </span>

            <button class="card-fav <?= $isFav ? 'active' : '' ?>"
                    onclick="toggleFav(event, this, <?= $m['id'] ?>)"
                    title="<?= $isFav ? 'Remove from favorites' : 'Add to favorites' ?>">
                <i class="fa-<?= $isFav ? 'solid' : 'regular' ?> fa-heart"></i>
            </button>

            <button class="card-wl <?= $isWL ? 'active' : '' ?>"
                    onclick="toggleWatchLater(event, this, <?= $m['id'] ?>)"
                    title="<?= $isWL ? 'Remove from watch later' : 'Add to watch later' ?>">
                <i class="fa-<?= $isWL ? 'solid' : 'regular' ?> fa-clock"></i>
            </button>

            <div class="card-body">
                <div class="card-title"><?= htmlspecialchars($m['title']) ?></div>
                <div class="card-meta">
                    <span><i class="fa-regular fa-calendar"></i> <?= $m['release_year'] ?></span>
                    <?php if ($isS && $m['episodes']): ?>
                        <span class="card-eps"><?= $m['episodes'] ?> eps</span>
                    <?php endif; ?>
                    <span class="card-rating">
                        <i class="fa-solid fa-star" style="font-size: 10px;"></i> <?= number_format($m['rating'], 1) ?>
                    </span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="empty-state" id="empty" style="display:none">
        <i class="fa-solid fa-film"></i>
        <h3>No titles found</h3>
        <p>Try adjusting your search or filter criteria</p>
        <button onclick="resetFilters()" class="btn btn-primary" style="margin-top:20px;">
            <i class="fa-solid fa-undo"></i> Reset Filters
        </button>
    </div>
</div>

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
let activeType = 'all';
let activeCategory = null;

function applyFilters() {
    const query = document.getElementById('search').value.toLowerCase();
    const cards = document.querySelectorAll('#catalog .card');
    let visible = 0;

    cards.forEach(card => {
        const matchType = activeType === 'all' || card.dataset.type === activeType;
        const matchCategory = !activeCategory || card.dataset.category === activeCategory;
        const matchQuery = !query || card.dataset.title.includes(query);

        const show = matchType && matchCategory && matchQuery;
        card.style.display = show ? 'block' : 'none';
        if (show) visible++;
    });

    const emptyState = document.getElementById('empty');
    const catalog = document.getElementById('catalog');
    
    if (visible === 0) {
        emptyState.style.display = 'block';
        catalog.style.display = 'none';
    } else {
        emptyState.style.display = 'none';
        catalog.style.display = 'grid';
    }
    
    document.getElementById('count-badge').textContent = visible + ' titles';
}

function resetFilters() {
    activeType = 'all';
    activeCategory = null;
    document.getElementById('search').value = '';
    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector('.filter-btn[data-filter="all"]').classList.add('active');
    applyFilters();
}

document.getElementById('search').addEventListener('input', applyFilters);

document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        if (btn.dataset.filter !== undefined) {
            activeType = btn.dataset.filter;
            activeCategory = null;
        } else if (btn.dataset.category !== undefined) {
            activeType = 'all';
            activeCategory = btn.dataset.category;
        }
        applyFilters();
    });
});

function toggleFav(event, btn, mediaId) {
    event.stopPropagation();
    fetch('database/toggle_favorite.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ media_id: mediaId })
    })
    .then(r => r.json())
    .then(data => {
        const icon = btn.querySelector('i');
        if (data.status === 'added') {
            btn.classList.add('active');
            icon.className = 'fa-solid fa-heart';
            showToast('✅ Added to favorites');
        } else if (data.status === 'removed') {
            btn.classList.remove('active');
            icon.className = 'fa-regular fa-heart';
            showToast('🗑️ Removed from favorites');
        }
    });
}

function toggleWatchLater(event, btn, mediaId) {
    event.stopPropagation();
    fetch('database/toggle_watchlater.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ media_id: mediaId })
    })
    .then(r => r.json())
    .then(data => {
        const icon = btn.querySelector('i');
        if (data.status === 'added') {
            btn.classList.add('active');
            icon.className = 'fa-solid fa-clock';
            showToast('⏰ Added to watch later');
        } else if (data.status === 'removed') {
            btn.classList.remove('active');
            icon.className = 'fa-regular fa-clock';
            showToast('🗑️ Removed from watch later');
        }
    });
}

function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.innerHTML = message;
    toast.style.cssText = `
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--gold);
        color: #111;
        padding: 12px 24px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 600;
        z-index: 1000;
        animation: slideUp 0.3s ease;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideDown 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 2000);
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateX(-50%) translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    }
    @keyframes slideDown {
        from {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
        to {
            opacity: 0;
            transform: translateX(-50%) translateY(20px);
        }
    }
`;
document.head.appendChild(style);
</script>

</body>
</html>