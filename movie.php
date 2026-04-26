<?php
require_once 'database/config.php';
if (!isLoggedIn()) { header("Location: signin.php"); exit; }

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM media WHERE id=?");
$stmt->bind_param("i",$id); $stmt->execute();
$m = $stmt->get_result()->fetch_assoc();
if (!$m) { header("Location: browse.php"); exit; }

$uid   = (int)$_SESSION['user_id'];
$isFav = $conn->prepare("SELECT id FROM favorites WHERE user_id=? AND media_id=?");
$isFav->bind_param("ii",$uid,$id); $isFav->execute();
$isFav = $isFav->get_result()->num_rows > 0;
$isWL  = $conn->prepare("SELECT id FROM watch_later WHERE user_id=? AND media_id=?");
$isWL->bind_param("ii",$uid,$id); $isWL->execute();
$isWL  = $isWL->get_result()->num_rows > 0;
$isS   = $m['type']==='series';
$stars = round($m['rating']/2);

$episodesBySeason = [];
if ($isS) {
    $ep = $conn->prepare("SELECT * FROM episodes WHERE media_id=? ORDER BY season_number,episode_number");
    $ep->bind_param("i",$id); $ep->execute();
    foreach ($ep->get_result()->fetch_all(MYSQLI_ASSOC) as $e)
        $episodesBySeason[$e['season_number']][] = $e;
}
addToWatchHistory($uid, $id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($m['title']) ?> — Streamy</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ── SINGLE NAVBAR ── -->
<nav class="navbar" id="navbar">
  <a href="browse.php" class="logo">Stream<span>y</span></a>
  <ul class="nav-links">
    <li><a href="browse.php">Home</a></li>
    <li><a href="browse.php?type=movie">Movies</a></li>
    <li><a href="browse.php?type=series">TV Shows</a></li>
    <li><a href="profile.php">My Library</a></li>
  </ul>
  <div class="nav-right">
    <a href="database/logout.php" class="nav-logout"><i class="fa-solid fa-sign-out-alt"></i> Logout</a>
    <div class="nav-avatar"><?= strtoupper(substr($_SESSION['username'],0,1)) ?></div>
  </div>
</nav>

<!-- ── HERO ── -->
<div class="hero">
  <div class="hero-bg">
    <img src="<?= htmlspecialchars($m['backdrop_url'] ?: $m['image_url']) ?>" alt="">
  </div>
  <div class="hero-content">
    <div class="hero-label"><i class="fa-solid fa-circle-play"></i> Now watching</div>
    <div class="hero-badges">
      <span class="type-badge <?= $isS ? 'badge-series' : 'badge-movie' ?>"><?= $isS ? 'Series' : 'Movie' ?></span>
      <span class="match-badge">98% Match</span>
      <span class="year-tag"><?= $m['release_year'] ?></span>
    </div>
    <h1 class="hero-title"><?= htmlspecialchars($m['title']) ?></h1>
    <div class="hero-genre-tags">
      <?php foreach (explode(',', $m['genre']) as $g): ?>
        <span class="g-tag"><?= htmlspecialchars(trim($g)) ?></span>
      <?php endforeach; ?>
    </div>
    <div class="hero-rating-row">
      <span class="rating-score"><?= number_format($m['rating'],1) ?><sub>/10</sub></span>
      <div class="stars-row">
        <?php for ($i=1;$i<=5;$i++): ?>
          <i class="fa-solid fa-star star-ic <?= $i<=$stars?'on':'' ?>"></i>
        <?php endfor; ?>
      </div>
      <?php if ($isS && $m['seasons']): ?>
        <div class="rating-sep"></div>
        <span class="seasons-label"><strong><?= $m['seasons'] ?></strong> Seasons</span>
      <?php endif; ?>
    </div>
    <p class="hero-desc"><?= htmlspecialchars($m['description']) ?></p>
    <div class="hero-btns">
      <?php if ($m['trailer_url']): ?>
      <button class="btn-play" onclick="openTrailer()">
        <i class="fa-solid fa-play"></i> Watch Trailer
      </button>
      <?php endif; ?>
      <button class="btn-ghost <?= $isWL?'active':'' ?>" id="wlBtn" onclick="toggleWL(<?= $m['id'] ?>)">
        <i class="fa-<?= $isWL?'solid':'regular' ?> fa-clock" id="wlIcon"></i>
        <span id="wlTxt"><?= $isWL?'Saved':'Watch Later' ?></span>
      </button>
      <button class="btn-icon <?= $isFav?'active':'' ?>" id="favBtn" onclick="toggleFav(<?= $m['id'] ?>)" title="<?= $isFav?'Remove':'Add to list' ?>">
        <i class="fa-<?= $isFav?'solid':'regular' ?> fa-heart" id="favIcon"></i>
      </button>
    </div>
  </div>
</div>

<!-- ── DETAILS STRIP ── -->
<div class="details-strip">
  <div class="details-grid">
    <?php if ($m['director']): ?>
    <div class="detail-cell">
      <div class="dc-label"><i class="fa-solid fa-clapperboard"></i> Director</div>
      <div class="dc-value"><?= htmlspecialchars($m['director']) ?></div>
    </div>
    <?php endif; ?>
    <?php if ($m['cast_list']): ?>
    <div class="detail-cell">
      <div class="dc-label"><i class="fa-solid fa-users"></i> Starring</div>
      <div class="dc-value"><?= htmlspecialchars($m['cast_list']) ?></div>
    </div>
    <?php endif; ?>
    <div class="detail-cell">
      <div class="dc-label"><i class="fa-solid fa-tag"></i> Genre</div>
      <div class="dc-value"><?= htmlspecialchars($m['genre']) ?></div>
    </div>
  </div>
</div>

<!-- ── EPISODES ── -->
<?php if ($isS && !empty($episodesBySeason)): ?>
<section class="episodes-section">
  <h2 class="section-heading"><span class="accent-bar"></span> Episodes</h2>
  <div class="season-tabs">
    <?php foreach (array_keys($episodesBySeason) as $sn): ?>
      <button class="s-tab <?= $sn===array_key_first($episodesBySeason)?'active':'' ?>" data-s="<?= $sn ?>">Season <?= $sn ?></button>
    <?php endforeach; ?>
  </div>
  <?php foreach ($episodesBySeason as $sn => $eps): ?>
  <div class="ep-grid season-eps" id="s-<?= $sn ?>" style="display:<?= $sn===array_key_first($episodesBySeason)?'grid':'none' ?>">
    <?php foreach ($eps as $ep): ?>
    <div class="ep-card">
      <div class="ep-thumb">
        <img src="<?= htmlspecialchars($ep['thumbnail_url']?:$m['image_url']) ?>" alt="<?= htmlspecialchars($ep['title']) ?>">
        <div class="ep-play-overlay"><i class="fa-solid fa-circle-play"></i></div>
      </div>
      <div class="ep-body">
        <div class="ep-num">Episode <?= $ep['episode_number'] ?></div>
        <div class="ep-name"><?= htmlspecialchars($ep['title']) ?></div>
        <div class="ep-desc"><?= htmlspecialchars(substr($ep['description'],0,120)) ?>...</div>
        <div class="ep-dur"><i class="fa-regular fa-clock"></i> <?= $ep['duration'] ?> min</div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endforeach; ?>
</section>
<?php endif; ?>

<!-- ── MODAL ── -->
<div class="modal" id="modal" onclick="if(event.target===this)closeTrailer()">
  <div class="modal-inner">
    <button class="modal-close" onclick="closeTrailer()"><i class="fa-solid fa-xmark"></i></button>
    <iframe id="tframe" src="" allow="autoplay;encrypted-media" allowfullscreen></iframe>
  </div>
</div>

<!-- ── FOOTER ── -->
<footer class="footer">
  <div class="footer-logo">Stream<span>y</span></div>
  <span class="footer-copy">© 2025 Streamy. All rights reserved.</span>
</footer>

<script>
window.addEventListener('scroll', () => {
  document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 10);
});

const trailerUrl = "<?= addslashes($m['trailer_url']) ?>";
function openTrailer(){
  if(!trailerUrl) return;
  document.getElementById('tframe').src = trailerUrl+'?autoplay=1';
  document.getElementById('modal').classList.add('open');
  document.body.style.overflow='hidden';
}
function closeTrailer(){
  document.getElementById('tframe').src='';
  document.getElementById('modal').classList.remove('open');
  document.body.style.overflow='';
}
document.addEventListener('keydown', e=>{ if(e.key==='Escape') closeTrailer(); });

function toggleFav(id){
  fetch('database/toggle_favorite.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({media_id:id})})
  .then(r=>r.json()).then(d=>{
    const btn=document.getElementById('favBtn'),i=document.getElementById('favIcon');
    if(d.status==='added'){btn.classList.add('active');i.className='fa-solid fa-heart';}
    else{btn.classList.remove('active');i.className='fa-regular fa-heart';}
  });
}
function toggleWL(id){
  fetch('database/toggle_watchlater.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({media_id:id})})
  .then(r=>r.json()).then(d=>{
    const btn=document.getElementById('wlBtn'),i=document.getElementById('wlIcon'),t=document.getElementById('wlTxt');
    if(d.status==='added'){btn.classList.add('active');i.className='fa-solid fa-clock';t.textContent='Saved';}
    else{btn.classList.remove('active');i.className='fa-regular fa-clock';t.textContent='Watch Later';}
  });
}
document.querySelectorAll('.s-tab').forEach(tab=>{
  tab.addEventListener('click',()=>{
    document.querySelectorAll('.s-tab').forEach(t=>t.classList.remove('active'));
    document.querySelectorAll('.season-eps').forEach(s=>s.style.display='none');
    tab.classList.add('active');
    document.getElementById('s-'+tab.dataset.s).style.display='grid';
  });
});
</script>
</body>
</html>