<?php
require_once 'database/config.php';
if (!isLoggedIn()) { header("Location: signin.php"); exit; }

$uid  = (int)$_SESSION['user_id'];
$user = getUserData($uid);

$favs    = $conn->query("SELECT m.* FROM media m JOIN favorites f ON f.media_id=m.id WHERE f.user_id=$uid ORDER BY f.added_at DESC")->fetch_all(MYSQLI_ASSOC);
$wl      = $conn->query("SELECT m.* FROM media m JOIN watch_later wl ON wl.media_id=m.id WHERE wl.user_id=$uid ORDER BY wl.added_at DESC")->fetch_all(MYSQLI_ASSOC);
$history = getWatchHistory($uid, 30);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile — Streamy</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="navbar" id="navbar">
  <a href="browse.php" class="logo">Stream<span>y</span></a>
  <ul class="nav-links">
    <li><a href="browse.php">Home</a></li>
    <li><a href="browse.php?type=movie">Movies</a></li>
    <li><a href="browse.php?type=series">TV Shows</a></li>
    <li><a href="profile.php" class="active">My Library</a></li>
  </ul>
  <div class="nav-right">
    <div class="nav-search-box">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" id="search" placeholder="Search titles...">
    </div>
    <a href="database/logout.php" class="nav-logout"><i class="fa-solid fa-sign-out-alt"></i> Logout</a>
    <div class="nav-avatar"><?= strtoupper(substr($_SESSION['username'],0,1)) ?></div>
  </div>
</nav>

<div class="page">

  <!-- ── PROFILE BANNER ── -->
  <div class="profile-banner">
    <div class="avatar-lg"><?= strtoupper(substr($_SESSION['username'],0,1)) ?></div>
    <div class="profile-info">
      <div class="profile-name"><?= htmlspecialchars($_SESSION['username']) ?></div>
      <div class="profile-detail"><i class="fa-regular fa-envelope"></i> <?= htmlspecialchars($user['email']) ?></div>
      <div class="profile-detail"><i class="fa-regular fa-calendar"></i> Member since <?= date('F Y', strtotime($user['created_at'])) ?></div>
      <div class="profile-badge"><i class="fa-solid fa-circle" style="font-size:7px"></i> Active member</div>
    </div>
  </div>

  <!-- ── STATS ── -->
  <div class="stats-row">
    <div class="stat-card">
      <div class="stat-icon"><i class="fa-solid fa-heart"></i></div>
      <div>
        <div class="stat-num"><?= count($favs) ?></div>
        <div class="stat-lbl">Favorites</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
      <div>
        <div class="stat-num"><?= count($wl) ?></div>
        <div class="stat-lbl">Watch Later</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="fa-solid fa-eye"></i></div>
      <div>
        <div class="stat-num"><?= count($history) ?></div>
        <div class="stat-lbl">Watched</div>
      </div>
    </div>
  </div>

  <!-- ── SEARCH BAR ── -->
  <div class="profile-search">
    <div class="search-wrapper">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" id="profileSearch" placeholder="Search in my list...">
    </div>
  </div>

  <!-- ── TAB BAR ── -->
  <div class="tab-bar">
    <button class="tab active" data-tab="favorites"><i class="fa-solid fa-heart"></i> My List</button>
    <button class="tab" data-tab="watchlater"><i class="fa-solid fa-clock"></i> Watch Later</button>
    <button class="tab" data-tab="history"><i class="fa-solid fa-history"></i> History</button>
    <button class="tab" data-tab="account"><i class="fa-solid fa-user"></i> Account</button>
  </div>

  <!-- ── FAVORITES ── -->
  <div class="tab-content active" id="tab-favorites">
    <div class="favs-container">
      <?php if (empty($favs)): ?>
      <div class="empty-state">
        <i class="fa-solid fa-heart"></i>
        <h3>No favorites yet</h3>
        <p>Click the heart icon on any title to add it here.</p>
      </div>
      <?php else: ?>
      <div class="mini-grid" id="favsGrid">
        <?php foreach ($favs as $f): $isS = $f['type']==='series'; ?>
        <div class="mini-card fav-item" data-title="<?= strtolower(htmlspecialchars($f['title'])) ?>" onclick="location.href='movie.php?id=<?= $f['id'] ?>'">
          <img src="<?= htmlspecialchars($f['image_url']) ?>" alt="<?= htmlspecialchars($f['title']) ?>" loading="lazy">
          <div class="mini-body">
            <div class="mini-title"><?= htmlspecialchars($f['title']) ?></div>
            <div class="mini-meta">
              <span class="mini-badge <?= $isS?'badge-series':'badge-movie' ?>"><?= $isS?'S':'M' ?></span>
              <span class="mini-rating"><i class="fa-solid fa-star" style="font-size:8px"></i> <?= number_format($f['rating'],1) ?></span>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- ── WATCH LATER ── -->
  <div class="tab-content" id="tab-watchlater">
    <div class="wl-container">
      <?php if (empty($wl)): ?>
      <div class="empty-state">
        <i class="fa-solid fa-clock"></i>
        <h3>Nothing saved yet</h3>
        <p>Click the clock icon on any title to save it for later.</p>
      </div>
      <?php else: ?>
      <div class="mini-grid" id="wlGrid">
        <?php foreach ($wl as $w): $isS = $w['type']==='series'; ?>
        <div class="mini-card wl-item" data-title="<?= strtolower(htmlspecialchars($w['title'])) ?>" onclick="location.href='movie.php?id=<?= $w['id'] ?>'">
          <img src="<?= htmlspecialchars($w['image_url']) ?>" alt="<?= htmlspecialchars($w['title']) ?>" loading="lazy">
          <div class="mini-body">
            <div class="mini-title"><?= htmlspecialchars($w['title']) ?></div>
            <div class="mini-meta">
              <span class="mini-badge <?= $isS?'badge-series':'badge-movie' ?>"><?= $isS?'S':'M' ?></span>
              <span class="mini-rating"><i class="fa-solid fa-star" style="font-size:8px"></i> <?= number_format($w['rating'],1) ?></span>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- ── HISTORY ── -->
  <div class="tab-content" id="tab-history">
    <?php if (empty($history)): ?>
    <div class="empty-state">
      <i class="fa-regular fa-eye"></i>
      <h3>No history yet</h3>
      <p>Start watching to see your activity here.</p>
    </div>
    <?php else: ?>
    <div class="hist-toolbar">
      <span class="hist-count"><?= count($history) ?> titles watched</span>
      <button class="clear-btn" onclick="clearHistory()"><i class="fa-solid fa-trash"></i> Clear all</button>
    </div>
    <div class="hist-list">
      <?php foreach ($history as $h): $isS = $h['type']==='series'; ?>
      <div class="hist-item" data-hid="<?= $h['id'] ?>" onclick="location.href='movie.php?id=<?= $h['media_id'] ?>'">
        <button class="remove-hist" onclick="removeHist(event,<?= $h['id'] ?>)"><i class="fa-solid fa-xmark"></i></button>
        <img class="hist-poster" src="<?= htmlspecialchars($h['image_url']) ?>" alt="<?= htmlspecialchars($h['title']) ?>">
        <div class="hist-info">
          <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
            <div class="hist-title"><?= htmlspecialchars($h['title']) ?></div>
            <span class="mini-badge <?= $isS?'badge-series':'badge-movie' ?>"><?= $isS?'Series':'Movie' ?></span>
          </div>
          <?php if ($h['episode_title']): ?>
          <div class="hist-ep"><i class="fa-solid fa-list"></i> S<?= $h['season_number'] ?>E<?= $h['episode_number'] ?> — <?= htmlspecialchars($h['episode_title']) ?></div>
          <?php endif; ?>
          <div class="hist-date"><i class="fa-regular fa-calendar"></i> <?= date('M j, Y \a\t g:i A', strtotime($h['watched_at'])) ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- ── ACCOUNT ── -->
  <div class="tab-content" id="tab-account">
    <div class="account-card">
      <h3><i class="fa-solid fa-user"></i> Account details</h3>
      <div class="account-row">
        <div class="account-label">Username</div>
        <div class="account-value"><?= htmlspecialchars($user['username']) ?></div>
      </div>
      <div class="account-row">
        <div class="account-label">Email</div>
        <div class="account-value"><?= htmlspecialchars($user['email']) ?></div>
      </div>
      <div class="account-row">
        <div class="account-label">Member since</div>
        <div class="account-value"><?= date('F j, Y', strtotime($user['created_at'])) ?></div>
      </div>
      <div class="account-row">
        <div class="account-label">Last login</div>
        <div class="account-value"><?= $user['last_login'] ? date('F j, Y g:i A', strtotime($user['last_login'])) : 'This session' ?></div>
      </div>
    </div>
    <a href="database/logout.php" class="signout-btn"><i class="fa-solid fa-sign-out-alt"></i> Sign out</a>
  </div>

</div>

<!-- ── FOOTER ── -->
<footer class="footer">
  <div class="footer-logo">Stream<span>y</span></div>
  <div class="footer-links">
    <a href="#">About</a>
    <a href="#">Help</a>
    <a href="#">Privacy</a>
    <a href="#">Terms</a>
  </div>
  <span class="footer-copy">© 2025 Streamy. All rights reserved.</span>
</footer>

<script>
// Navbar scroll
window.addEventListener('scroll', () => {
  const navbar = document.getElementById('navbar');
  if (navbar) {
    navbar.classList.toggle('scrolled', window.scrollY > 10);
  }
});

// Tabs
document.querySelectorAll('.tab').forEach(tab => {
  tab.addEventListener('click', () => {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    tab.classList.add('active');
    document.getElementById('tab-' + tab.dataset.tab).classList.add('active');
  });
});

// Search in favorites and watch later
const searchInput = document.getElementById('profileSearch');
if (searchInput) {
  searchInput.addEventListener('input', function() {
    const query = this.value.toLowerCase().trim();
    
    // Search in favorites
    const favItems = document.querySelectorAll('.fav-item');
    if (favItems.length > 0) {
      let favVisible = 0;
      favItems.forEach(item => {
        const title = item.dataset.title;
        const match = !query || title.includes(query);
        item.style.display = match ? 'block' : 'none';
        if (match) favVisible++;
      });
      const favContainer = document.querySelector('#tab-favorites .mini-grid');
      const favEmpty = document.querySelector('#tab-favorites .empty-state');
      if (favContainer && favVisible === 0 && favItems.length > 0) {
        if (!document.querySelector('#tab-favorites .search-empty')) {
          const emptyMsg = document.createElement('div');
          emptyMsg.className = 'empty-state search-empty';
          emptyMsg.innerHTML = '<i class="fa-solid fa-search"></i><h3>No matching titles</h3><p>Try a different search term</p>';
          favContainer.parentNode.appendChild(emptyMsg);
        }
        favContainer.style.display = 'none';
      } else if (favContainer) {
        const searchEmpty = document.querySelector('#tab-favorites .search-empty');
        if (searchEmpty) searchEmpty.remove();
        favContainer.style.display = 'grid';
      }
    }
    
    // Search in watch later
    const wlItems = document.querySelectorAll('.wl-item');
    if (wlItems.length > 0) {
      let wlVisible = 0;
      wlItems.forEach(item => {
        const title = item.dataset.title;
        const match = !query || title.includes(query);
        item.style.display = match ? 'block' : 'none';
        if (match) wlVisible++;
      });
      const wlContainer = document.querySelector('#tab-watchlater .mini-grid');
      const wlEmpty = document.querySelector('#tab-watchlater .empty-state');
      if (wlContainer && wlVisible === 0 && wlItems.length > 0) {
        if (!document.querySelector('#tab-watchlater .search-empty')) {
          const emptyMsg = document.createElement('div');
          emptyMsg.className = 'empty-state search-empty';
          emptyMsg.innerHTML = '<i class="fa-solid fa-search"></i><h3>No matching titles</h3><p>Try a different search term</p>';
          wlContainer.parentNode.appendChild(emptyMsg);
        }
        wlContainer.style.display = 'none';
      } else if (wlContainer) {
        const searchEmpty = document.querySelector('#tab-watchlater .search-empty');
        if (searchEmpty) searchEmpty.remove();
        wlContainer.style.display = 'grid';
      }
    }
  });
}

function showToast(msg){
  const t = document.createElement('div');
  t.className = 'toast'; t.textContent = msg;
  document.body.appendChild(t);
  setTimeout(() => { t.style.transition='opacity .3s'; t.style.opacity='0'; setTimeout(()=>t.remove(),300); }, 2200);
}

function clearHistory(){
  if (!confirm('Clear all watch history? This cannot be undone.')) return;
  fetch('database/clear_history.php', {method:'POST', headers:{'Content-Type':'application/json'}})
  .then(r=>r.json()).then(d => {
    if (d.status==='success'){ showToast('✅ History cleared'); setTimeout(()=>location.reload(),800); }
  });
}

function removeHist(e, hid){
  e.stopPropagation();
  fetch('database/remove_history_item.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({history_id:hid})})
  .then(r=>r.json()).then(d => {
    if (d.status==='success'){
      const el = document.querySelector(`.hist-item[data-hid="${hid}"]`);
      if (el) {
        el.style.transition = 'all .3s';
        el.style.opacity = '0';
        el.style.transform = 'translateX(-20px)';
        setTimeout(() => { el.remove(); showToast('Removed from history'); }, 300);
      }
    }
  });
}
</script>
</body>
</html>