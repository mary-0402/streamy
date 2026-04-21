<?php
require_once 'database/config.php';

if (!isLoggedIn()) {
    header("Location: signin.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM media WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$m = $stmt->get_result()->fetch_assoc();

if (!$m) {
    header("Location: browse.php");
    exit;
}

$uid = (int)$_SESSION['user_id'];
$fstmt = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND media_id = ?");
$fstmt->bind_param("ii", $uid, $id);
$fstmt->execute();
$isFav = $fstmt->get_result()->num_rows > 0;

$wlstmt = $conn->prepare("SELECT id FROM watch_later WHERE user_id = ? AND media_id = ?");
$wlstmt->bind_param("ii", $uid, $id);
$wlstmt->execute();
$isWL = $wlstmt->get_result()->num_rows > 0;

$isSeries = $m['type'] === 'series';
$stars = round($m['rating'] / 2);

// Get episodes if series
$episodes = [];
if ($isSeries) {
    $epStmt = $conn->prepare("SELECT * FROM episodes WHERE media_id = ? ORDER BY season_number, episode_number");
    $epStmt->bind_param("i", $id);
    $epStmt->execute();
    $episodes = $epStmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Group episodes by season
$episodesBySeason = [];
foreach ($episodes as $ep) {
    $episodesBySeason[$ep['season_number']][] = $ep;
}

// Add to watch history
addToWatchHistory($uid, $id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($m['title']) ?> — Streamy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="streamy.css">
    <style>
        /* Backdrop styles */
        .backdrop {
            position: relative;
            width: 100%;
            height: 60vh;
            min-height: 400px;
            overflow: hidden;
        }
        .backdrop img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.3);
        }
        .backdrop-gradient {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 200px;
            background: linear-gradient(to top, var(--bg), transparent);
        }
        
        /* Detail layout */
        .detail {
            display: flex;
            gap: 40px;
            padding: 0 64px 60px;
            margin-top: -120px;
            position: relative;
            z-index: 10;
            flex-wrap: wrap;
        }
        
        /* Poster column */
        .poster-col {
            flex-shrink: 0;
            width: 280px;
        }
        .poster-frame {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
            border: 2px solid var(--border);
        }
        .poster-frame img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        /* Poster actions */
        .poster-actions {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .btn-trailer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 12px;
            background: var(--red);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-trailer:hover {
            background: var(--red-light);
            transform: translateY(-2px);
        }
        .btn-fav-detail {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 12px;
            background: var(--surface);
            border: 1px solid var(--border);
            color: var(--text);
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-fav-detail:hover {
            border-color: var(--red);
            color: var(--red-light);
        }
        .btn-fav-detail.active {
            background: rgba(139,26,42,0.2);
            border-color: var(--red);
            color: var(--red-light);
        }
        
        /* Info column */
        .info-col {
            flex: 1;
            min-width: 300px;
        }
        .type-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }
        .badge-movie-lg {
            background: var(--red);
            color: white;
        }
        .badge-series-lg {
            background: var(--gold);
            color: #111;
        }
        .detail-title {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            font-weight: 900;
            color: var(--white);
            margin-bottom: 20px;
            line-height: 1.2;
        }
        
        /* Meta pills */
        .meta-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 24px;
        }
        .pill {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            font-size: 12px;
            color: var(--text);
        }
        .pill i {
            color: var(--red-light);
            font-size: 12px;
        }
        .pill-gold i {
            color: var(--gold);
        }
        
        /* Rating */
        .rating-row {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 24px;
        }
        .rating-num {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            font-weight: 900;
            color: var(--red-light);
        }
        .rating-num sub {
            font-size: 18px;
            color: var(--muted);
        }
        .stars {
            display: flex;
            gap: 5px;
        }
        .star {
            font-size: 18px;
            color: #333;
        }
        .star.filled {
            color: var(--gold);
        }
        
        /* Synopsis */
        .synopsis {
            font-size: 15px;
            line-height: 1.7;
            color: var(--muted);
            margin-bottom: 30px;
        }
        
        /* Detail meta */
        .detail-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }
        .meta-item label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--muted);
            margin-bottom: 5px;
        }
        .meta-item span {
            font-size: 14px;
            color: var(--text);
        }
        
        /* Episodes section */
        .episodes-section {
            padding: 0 64px 60px;
        }
        .section-header {
            margin-bottom: 30px;
        }
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--white);
        }
        .season-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 30px;
        }
        .season-tab {
            padding: 8px 24px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
            color: var(--text);
        }
        .season-tab:hover {
            border-color: var(--red);
        }
        .season-tab.active {
            background: var(--red);
            border-color: var(--red);
            color: white;
        }
        .episodes-grid {
            display: grid;
            gap: 16px;
        }
        .episode-item {
            display: flex;
            gap: 20px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .episode-item:hover {
            border-color: var(--red);
            transform: translateX(8px);
            background: var(--bg3);
        }
        .episode-thumb {
            width: 160px;
            height: 90px;
            background: var(--bg);
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
        }
        .episode-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .episode-info {
            flex: 1;
        }
        .episode-title {
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 8px;
            color: var(--white);
        }
        .episode-desc {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 8px;
            line-height: 1.5;
        }
        .episode-duration {
            font-size: 12px;
            color: var(--gold);
        }
        
        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.95);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.open {
            display: flex;
        }
        .modal-inner {
            position: relative;
            width: 90%;
            max-width: 1000px;
        }
        .modal-close {
            position: absolute;
            top: -50px;
            right: 0;
            background: none;
            border: none;
            color: white;
            font-size: 30px;
            cursor: pointer;
        }
        .modal-inner iframe {
            width: 100%;
            aspect-ratio: 16/9;
            border: none;
            border-radius: 12px;
        }
        
        /* Responsive */
        @media (max-width: 968px) {
            .detail {
                padding: 0 24px 40px;
                margin-top: -80px;
            }
            .poster-col {
                width: 220px;
            }
            .detail-title {
                font-size: 36px;
            }
            .episodes-section {
                padding: 0 24px 40px;
            }
        }
        @media (max-width: 768px) {
            .detail {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .poster-col {
                width: 200px;
            }
            .meta-pills {
                justify-content: center;
            }
            .rating-row {
                justify-content: center;
            }
            .detail-meta {
                text-align: left;
            }
            .episode-item {
                flex-direction: column;
            }
            .episode-thumb {
                width: 100%;
                height: auto;
                aspect-ratio: 16/9;
            }
        }
        @media (max-width: 480px) {
            .detail-title {
                font-size: 28px;
            }
            .rating-num {
                font-size: 36px;
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

<!-- Backdrop -->
<div class="backdrop">
    <img src="<?= htmlspecialchars($m['backdrop_url'] ?: $m['image_url']) ?>" alt="<?= htmlspecialchars($m['title']) ?>">
    <div class="backdrop-gradient"></div>
</div>

<!-- Detail -->
<div class="detail">
    <!-- Poster -->
    <div class="poster-col">
        <div class="poster-frame">
            <img src="<?= htmlspecialchars($m['image_url']) ?>" alt="<?= htmlspecialchars($m['title']) ?>">
        </div>
        <div class="poster-actions">
            <?php if ($m['trailer_url']): ?>
            <button class="btn-trailer" onclick="openTrailer()">
                <i class="fa-solid fa-play"></i> Watch Trailer
            </button>
            <?php endif; ?>
            <button class="btn-fav-detail <?= $isFav ? 'active' : '' ?>" id="favBtn" onclick="toggleFav(<?= $m['id'] ?>)">
                <i class="fa-<?= $isFav ? 'solid' : 'regular' ?> fa-heart" id="favIcon"></i>
                <span id="favText"><?= $isFav ? 'In My List' : 'Add to My List' ?></span>
            </button>
            <button class="btn-fav-detail <?= $isWL ? 'active' : '' ?>" id="wlBtn" onclick="toggleWatchLater(<?= $m['id'] ?>)">
                <i class="fa-<?= $isWL ? 'solid' : 'regular' ?> fa-clock" id="wlIcon"></i>
                <span id="wlText"><?= $isWL ? 'In Watch Later' : 'Watch Later' ?></span>
            </button>
        </div>
    </div>

    <!-- Info -->
    <div class="info-col">
        <span class="type-badge <?= $isSeries ? 'badge-series-lg' : 'badge-movie-lg' ?>">
            <?= $isSeries ? 'SERIES' : 'MOVIE' ?>
        </span>
        
        <h1 class="detail-title"><?= htmlspecialchars($m['title']) ?></h1>
        
        <div class="meta-pills">
            <span class="pill"><i class="fa-solid fa-calendar"></i> <?= $m['release_year'] ?></span>
            <span class="pill"><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($m['genre']) ?></span>
            <span class="pill"><i class="fa-solid fa-layer-group"></i> <?= ucfirst($m['category']) ?></span>
            <?php if ($isSeries && $m['seasons']): ?>
            <span class="pill pill-gold"><i class="fa-solid fa-tv"></i> <?= $m['seasons'] ?> Seasons</span>
            <?php endif; ?>
        </div>

        <div class="rating-row">
            <div class="rating-num">
                <?= number_format($m['rating'], 1) ?><sub>/10</sub>
            </div>
            <div class="stars">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fa-solid fa-star star <?= $i <= $stars ? 'filled' : '' ?>"></i>
                <?php endfor; ?>
            </div>
        </div>

        <p class="synopsis"><?= nl2br(htmlspecialchars($m['description'])) ?></p>

        <div class="detail-meta">
            <?php if ($m['director']): ?>
            <div class="meta-item">
                <label><i class="fa-solid fa-clapperboard"></i> Director</label>
                <span><?= htmlspecialchars($m['director']) ?></span>
            </div>
            <?php endif; ?>
            <?php if ($m['cast_list']): ?>
            <div class="meta-item">
                <label><i class="fa-solid fa-users"></i> Cast</label>
                <span><?= htmlspecialchars($m['cast_list']) ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Episodes -->
<?php if ($isSeries && !empty($episodes)): ?>
<div class="episodes-section">
    <div class="section-header">
        <div class="section-title">
            <i class="fa-solid fa-list"></i> Episodes
        </div>
    </div>
    
    <div class="season-tabs" id="seasonTabs">
        <?php foreach (array_keys($episodesBySeason) as $season): ?>
            <button class="season-tab <?= $season === 1 ? 'active' : '' ?>" data-season="<?= $season ?>">
                Season <?= $season ?>
            </button>
        <?php endforeach; ?>
    </div>
    
    <?php foreach ($episodesBySeason as $seasonNum => $seasonEpisodes): ?>
        <div class="episodes-grid" id="season-<?= $seasonNum ?>" style="display: <?= $seasonNum === 1 ? 'grid' : 'none' ?>">
            <?php foreach ($seasonEpisodes as $ep): ?>
                <div class="episode-item" onclick="playEpisode(<?= $ep['id'] ?>, <?= $m['id'] ?>)">
                    <div class="episode-thumb">
                        <img src="<?= htmlspecialchars($ep['thumbnail_url'] ?: $m['image_url']) ?>" alt="<?= htmlspecialchars($ep['title']) ?>">
                    </div>
                    <div class="episode-info">
                        <div class="episode-title">
                            Episode <?= $ep['episode_number'] ?>: <?= htmlspecialchars($ep['title']) ?>
                        </div>
                        <div class="episode-desc"><?= htmlspecialchars(substr($ep['description'], 0, 120)) ?>...</div>
                        <div class="episode-duration">
                            <i class="fa-regular fa-clock"></i> <?= $ep['duration'] ?> minutes
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Trailer Modal -->
<div class="modal-overlay" id="trailerModal" onclick="closeTrailer(event)">
    <div class="modal-inner">
        <button class="modal-close" onclick="closeTrailerBtn()">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <iframe id="trailerFrame" src="" allow="autoplay; encrypted-media" allowfullscreen></iframe>
    </div>
</div>

<footer class="footer">
    <div class="footer-logo">Stream<span>y</span></div>
    <span>© 2025 Streamy. All rights reserved.</span>
</footer>

<script>
const trailerUrl = "<?= addslashes($m['trailer_url']) ?>";

function openTrailer() {
    if (trailerUrl) {
        document.getElementById('trailerFrame').src = trailerUrl + '?autoplay=1';
        document.getElementById('trailerModal').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
}

function closeTrailer(e) {
    if (e.target === document.getElementById('trailerModal')) {
        closeTrailerBtn();
    }
}

function closeTrailerBtn() {
    document.getElementById('trailerFrame').src = '';
    document.getElementById('trailerModal').classList.remove('open');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeTrailerBtn();
    }
});

function toggleFav(mediaId) {
    fetch('database/toggle_favorite.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ media_id: mediaId })
    })
    .then(response => response.json())
    .then(data => {
        const btn = document.getElementById('favBtn');
        const icon = document.getElementById('favIcon');
        const text = document.getElementById('favText');
        
        if (data.status === 'added') {
            btn.classList.add('active');
            icon.className = 'fa-solid fa-heart';
            text.textContent = 'In My List';
        } else if (data.status === 'removed') {
            btn.classList.remove('active');
            icon.className = 'fa-regular fa-heart';
            text.textContent = 'Add to My List';
        }
    });
}

function toggleWatchLater(mediaId) {
    fetch('database/toggle_watchlater.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ media_id: mediaId })
    })
    .then(response => response.json())
    .then(data => {
        const btn = document.getElementById('wlBtn');
        const icon = document.getElementById('wlIcon');
        const text = document.getElementById('wlText');
        
        if (data.status === 'added') {
            btn.classList.add('active');
            icon.className = 'fa-solid fa-clock';
            text.textContent = 'In Watch Later';
        } else if (data.status === 'removed') {
            btn.classList.remove('active');
            icon.className = 'fa-regular fa-clock';
            text.textContent = 'Watch Later';
        }
    });
}

function playEpisode(episodeId, mediaId) {
    fetch('database/add_to_history.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ media_id: mediaId, episode_id: episodeId })
    });
    alert('🎬 Playing: Episode ' + episodeId + '\nVideo player integration will be added here.');
}

// Season tabs
document.querySelectorAll('.season-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        const season = this.dataset.season;
        
        document.querySelectorAll('.season-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        
        document.querySelectorAll('[id^="season-"]').forEach(seasonDiv => {
            seasonDiv.style.display = 'none';
        });
        
        document.getElementById(`season-${season}`).style.display = 'grid';
    });
});
</script>

</body>
</html>