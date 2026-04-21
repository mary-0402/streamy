<?php
require_once 'database/config.php';

if (!isLoggedIn()) {
    header("Location: signin.php");
    exit;
}

$profiles = getUserProfiles($_SESSION['user_id']);
$user = getUserData($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profiles — Streamy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="streamy.css">
    <style>
        .profiles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 30px;
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
        }
        .profile-card {
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .profile-card:hover {
            transform: scale(1.05);
        }
        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: bold;
            color: white;
            border: 3px solid transparent;
            transition: border-color 0.2s;
        }
        .profile-card:hover .profile-avatar {
            border-color: var(--red);
        }
        .profile-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .profile-info {
            font-size: 12px;
            color: var(--muted);
        }
        .add-profile {
            background: var(--surface);
            border: 2px dashed var(--border);
        }
        .add-profile .profile-avatar {
            background: var(--bg);
            font-size: 64px;
            color: var(--muted);
        }
        .account-info {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            margin: 20px 40px;
        }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="browse.php" class="logo">Stream<span>y</span></a>
    <ul class="nav-links">
        <li><a href="browse.php">Browse</a></li>
        <li><a href="favorites.php">My List</a></li>
    </ul>
    <div class="nav-actions">
        <a href="database/logout.php" class="btn btn-outline">Logout</a>
    </div>
</nav>

<div class="page-header">
    <h1 class="page-title">Who's Watching?</h1>
    <p class="page-subtitle">Select a profile to continue</p>
</div>

<div class="profiles-grid">
    <?php foreach ($profiles as $profile): ?>
        <div class="profile-card" onclick="selectProfile(<?= $profile['id'] ?>)">
            <div class="profile-avatar" style="background: <?= $profile['avatar_color'] ?>">
                <?= strtoupper(substr($profile['profile_name'], 0, 1)) ?>
            </div>
            <div class="profile-name"><?= htmlspecialchars($profile['profile_name']) ?></div>
            <div class="profile-info">
                <?php if ($profile['is_kids_profile']): ?>
                    <i class="fa-solid fa-child"></i> Kids Mode
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
    
    <div class="profile-card add-profile" onclick="addProfile()">
        <div class="profile-avatar">
            <i class="fa-solid fa-plus"></i>
        </div>
        <div class="profile-name">Add Profile</div>
    </div>
</div>

<div class="account-info">
    <div class="detail-meta">
        <div class="meta-item">
            <label>Account Email</label>
            <span><?= htmlspecialchars($user['email']) ?></span>
        </div>
        <div class="meta-item">
            <label>Member Since</label>
            <span><?= date('F j, Y', strtotime($user['created_at'])) ?></span>
        </div>
        <div class="meta-item">
            <label>Last Login</label>
            <span><?= $user['last_login'] ? date('F j, Y g:i A', strtotime($user['last_login'])) : 'Never' ?></span>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="footer-logo">Stream<span>y</span></div>
    <span>© 2025 Streamy</span>
</footer>

<script>
function selectProfile(profileId) {
    fetch('database/select_profile.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ profile_id: profileId })
    }).then(() => {
        window.location.href = 'browse.php';
    });
}

function addProfile() {
    let name = prompt('Enter profile name:');
    if (name) {
        fetch('database/add_profile.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ profile_name: name })
        }).then(() => location.reload());
    }
}
</script>

</body>
</html>