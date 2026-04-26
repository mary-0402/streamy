<?php
require_once 'database/config.php';
if (!isLoggedIn()) { header("Location: signin.php"); exit; }

$uid = (int)$_SESSION['user_id'];

// Get only TV shows/series
$all = $conn->query("SELECT * FROM media WHERE type='series' ORDER BY rating DESC")->fetch_all(MYSQLI_ASSOC);
$favIds = array_column($conn->query("SELECT media_id FROM favorites WHERE user_id=$uid")->fetch_all(MYSQLI_ASSOC), 'media_id');
$wlIds = array_column($conn->query("SELECT media_id FROM watch_later WHERE user_id=$uid")->fetch_all(MYSQLI_ASSOC), 'media_id');
$featured = $conn->query("SELECT * FROM media WHERE type='series' ORDER BY rating DESC LIMIT 1")->fetch_assoc();

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
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TV Shows — Streamy</title>
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
    <li><a href="movies.php">Movies</a></li>
    <li><a href="tv_shows.php" class="active">TV Shows</a></li>
    <li><a href="profile.php">My Library</a></li>
  </ul>
  <div class="nav-right">
    <div class="nav-search-box">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" id="search" placeholder="Search TV shows...">
    </div>
    <a href="database/logout.php" class="nav-logout"><i class="fa-solid fa-sign-out-alt"></i> Logout</a>
    <div class="nav-avatar"><?= strtoupper(substr($_SESSION['username'],0,1)) ?></div>
  </div>
</nav>

<!-- ── HERO ── -->
<?php if ($featured): ?>
<div class="hero">
  <div class="hero-bg">
    <img src="<?= htmlspecialchars($featured['backdrop_url'] ?: $featured['image_url']) ?>" alt="<?= htmlspecialchars($featured['title']) ?>">
  </div>
  <div class="hero-content">
    <div class="hero-label"><i class="fa-solid fa-tv"></i> Featured Series</div>
    <h1 class="hero-title"><?= htmlspecialchars($featured['title']) ?></h1>
    <div class="hero-chips">
      <span class="hero-match">98% Match</span>
      <span class="hero-chip"><?= $featured['release_year'] ?></span>
      <span class="hero-chip"><?= htmlspecialchars($featured['genre']) ?></span>
      <?php if ($featured['seasons']): ?>
      <span class="hero-chip"><?= $featured['seasons'] ?> Seasons</span>
      <?php endif; ?>
    </div>
    <p class="hero-desc"><?= htmlspecialchars(substr($featured['description'],0,200)) ?>...</p>
    <div class="hero-btns">
      <button class="btn-play" onclick="location.href='movie.php?id=<?= $featured['id'] ?>'">
        <i class="fa-solid fa-play"></i> Play now
      </button>
      <a href="movie.php?id=<?= $featured['id'] ?>" class="btn-info">
        <i class="fa-solid fa-circle-info"></i> More info
      </a>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- ── FILTERS (TOP) ── -->
<div class="filter-bar" id="filters">
  <?php foreach ($genres as $g): ?>
  <button class="filter-btn <?= $g['key']==='all'?'active':'' ?>" data-genre="<?= $g['key'] ?>"><?= $g['label'] ?></button>
  <?php endforeach; ?>
</div>

<!-- ── GALLERY ── -->
<div class="gallery-wrapper">
  <div class="gallery-header">
    <div class="gallery-title">
      <span class="row-title-accent"></span> All TV Shows
      <span class="count-badge" id="count-badge"><?= count($all) ?> series</span>
    </div>
  </div>

  <div class="grid" id="catalog">
    <?php foreach ($all as $m):
      $isFav = in_array($m['id'],$favIds); $isWL = in_array($m['id'],$wlIds); ?>
    <div class="card animated-card"
         data-type="<?= $m['type'] ?>"
         data-genre="<?= $m['category'] ?>"
         data-title="<?= strtolower(htmlspecialchars($m['title'])) ?>"
         onclick="location.href='movie.php?id=<?= $m['id'] ?>'">
      <img class="card-poster" src="<?= htmlspecialchars($m['image_url']) ?>" alt="<?= htmlspecialchars($m['title']) ?>" loading="lazy">
      <span class="card-badge badge-series">S</span>
      <button class="card-fav <?= $isFav?'active':'' ?>" onclick="toggleFav(event,this,<?= $m['id'] ?>)"><i class="fa-<?= $isFav?'solid':'regular' ?> fa-heart"></i></button>
      <button class="card-wl <?= $isWL?'active':'' ?>" onclick="toggleWL(event,this,<?= $m['id'] ?>)"><i class="fa-<?= $isWL?'solid':'regular' ?> fa-clock"></i></button>
      <div class="card-body">
        <div class="card-title"><?= htmlspecialchars($m['title']) ?></div>
        <div class="card-meta">
          <span><?= $m['release_year'] ?></span>
          <?php if ($m['seasons']): ?><span><?= $m['seasons'] ?> Seasons</span><?php endif; ?>
          <span class="card-rating"><i class="fa-solid fa-star" style="font-size:8px"></i> <?= number_format($m['rating'],1) ?></span>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="empty-state" id="empty" style="display:none">
    <i class="fa-solid fa-tv"></i>
    <h3>No TV shows found</h3>
    <p>Try a different filter or search term</p>
    <button onclick="resetFilters()" style="margin-top:20px;padding:10px 22px;background:var(--red);color:#fff;border:none;border-radius:5px;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;cursor:pointer">
      <i class="fa-solid fa-undo"></i> Reset filters
    </button>
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
window.addEventListener('scroll', () => {
  document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 10);
});

let activeGenre = 'all';

function applyFilters(){
  const q = document.getElementById('search').value.toLowerCase().trim();
  let vis = 0;
  document.querySelectorAll('#catalog .card').forEach(c => {
    const gMatch = activeGenre === 'all' || c.dataset.genre === activeGenre;
    const qMatch = !q || c.dataset.title.includes(q);
    const show = gMatch && qMatch;
    c.style.display = show ? 'block' : 'none';
    if (show) vis++;
  });
  document.getElementById('empty').style.display = vis ? 'none' : 'block';
  document.getElementById('catalog').style.display = vis ? 'grid' : 'none';
  document.getElementById('count-badge').textContent = vis + ' series';
}

function resetFilters(){
  activeGenre = 'all';
  document.getElementById('search').value = '';
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  document.querySelector('.filter-btn[data-genre="all"]').classList.add('active');
  applyFilters();
}

document.getElementById('search').addEventListener('input', applyFilters);

document.querySelectorAll('.filter-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    activeGenre = btn.dataset.genre;
    applyFilters();
  });
});

function toggleFav(e, btn, id){
  e.stopPropagation();
  fetch('database/toggle_favorite.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({media_id:id})})
  .then(r=>r.json()).then(d=>{
    const i = btn.querySelector('i');
    if(d.status==='added'){ btn.classList.add('active'); i.className='fa-solid fa-heart'; showToast('❤️ Added to My List'); }
    else { btn.classList.remove('active'); i.className='fa-regular fa-heart'; showToast('Removed from My List'); }
  });
}

function toggleWL(e, btn, id){
  e.stopPropagation();
  fetch('database/toggle_watchlater.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({media_id:id})})
  .then(r=>r.json()).then(d=>{
    const i = btn.querySelector('i');
    if(d.status==='added'){ btn.classList.add('active'); i.className='fa-solid fa-clock'; showToast('⏰ Watch Later saved'); }
    else { btn.classList.remove('active'); i.className='fa-regular fa-clock'; showToast('Removed from Watch Later'); }
  });
}

function showToast(msg){
  const t = document.createElement('div');
  t.className = 'toast';
  t.textContent = msg;
  document.body.appendChild(t);
  setTimeout(()=>{ t.style.transition='opacity .3s'; t.style.opacity='0'; setTimeout(()=>t.remove(),300); }, 2400);
}
</script>
</body>
</html>