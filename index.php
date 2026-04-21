<?php
// index.php - Landing page
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: browse.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Streamy - Plateforme de Streaming</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="streamy.css">
    <style>
        /* Hero Section améliorée */
        .hero {
            position: relative;
            min-height: 90vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
            padding: 80px 20px;
        }
        
        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 50%, #0a0a0a 100%);
            z-index: 0;
        }
        
        .hero-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 50%, rgba(139,26,42,0.3) 0%, transparent 50%);
            z-index: 1;
        }
        
        .hero-bg::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.05"><path fill="white" d="M20,20 L80,20 L80,80 L20,80 Z"/><circle cx="50" cy="50" r="10"/></svg>') repeat;
            background-size: 50px 50px;
            z-index: 1;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            animation: fadeInUp 1s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 80px;
            font-weight: 900;
            background: linear-gradient(135deg, #ffffff 0%, #e08090 50%, #ffffff 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 20px;
            letter-spacing: 3px;
        }
        
        .hero-title span {
            background: linear-gradient(135deg, #e08090 0%, #ff6b7c 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .hero-desc {
            font-size: 18px;
            color: #b0a0a8;
            margin-bottom: 40px;
            line-height: 1.8;
        }
        
        .hero-cta {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-hero {
            padding: 14px 36px;
            font-size: 16px;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        
        .btn-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(139,26,42,0.4);
        }
        
        /* Features Section */
        .features {
            padding: 80px 40px;
            background: var(--bg2);
            border-top: 1px solid var(--border);
        }
        
        .features-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 15px;
        }
        
        .section-subtitle {
            font-size: 16px;
            color: var(--muted);
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }
        
        .feature-card {
            background: var(--bg3);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 40px 30px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            border-color: var(--red);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--red), var(--red-light));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 32px;
            color: white;
            transition: all 0.3s ease;
        }
        
        .feature-card:hover .feature-icon {
            transform: scale(1.1);
            box-shadow: 0 0 30px rgba(139,26,42,0.5);
        }
        
        .feature-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 15px;
        }
        
        .feature-text {
            font-size: 14px;
            color: var(--muted);
            line-height: 1.6;
        }
        
        /* Popular Section */
        .popular {
            padding: 80px 40px;
            background: var(--bg);
        }
        
        .popular-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .popular-item {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .popular-item:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 30px rgba(0,0,0,0.5);
        }
        
        .popular-item img {
            width: 100%;
            aspect-ratio: 2/3;
            object-fit: cover;
        }
        
        .popular-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
            padding: 20px;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }
        
        .popular-item:hover .popular-overlay {
            transform: translateY(0);
        }
        
        .popular-title {
            color: white;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .popular-rating {
            color: var(--gold);
            font-size: 12px;
        }
        
        /* CTA Section */
        .cta-section {
            padding: 80px 40px;
            background: linear-gradient(135deg, var(--red), var(--red-light));
            text-align: center;
        }
        
        .cta-title {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            font-weight: 900;
            color: white;
            margin-bottom: 20px;
        }
        
        .cta-text {
            font-size: 18px;
            color: rgba(255,255,255,0.9);
            margin-bottom: 30px;
        }
        
        .btn-cta {
            background: white;
            color: var(--red);
            padding: 15px 40px;
            font-size: 16px;
            font-weight: 700;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        
        .btn-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 48px;
            }
            
            .hero-desc {
                font-size: 16px;
            }
            
            .features {
                padding: 60px 20px;
            }
            
            .section-title {
                font-size: 28px;
            }
            
            .cta-title {
                font-size: 32px;
            }
            
            .popular {
                padding: 60px 20px;
            }
        }
        
        @media (max-width: 480px) {
            .hero-title {
                font-size: 36px;
            }
            
            .btn-hero {
                padding: 12px 24px;
                font-size: 14px;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="logo">Stream<span>y</span></div>
    <ul class="nav-links">
        <li><a href="#" class="active">Accueil</a></li>
        <li><a href="browse.php">Catalogue</a></li>
    </ul>
    <div class="nav-actions">
        <a href="signin.php" class="btn btn-outline">Connexion</a>
        <a href="signup.php" class="btn btn-primary">Inscription</a>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-content">
        <h1 class="hero-title">Stream<span>y</span></h1>
        <p class="hero-desc">
            Découvrez une expérience de streaming unique avec des milliers de films et séries.<br>
            Qualité HD, sans publicité, et accessible partout.
        </p>
        <div class="hero-cta">
            <a href="signup.php" class="btn btn-primary btn-hero">
                <i class="fa-solid fa-play"></i> Commencer gratuitement
            </a>
            <a href="browse.php" class="btn btn-outline btn-hero">
                <i class="fa-solid fa-magnifying-glass"></i> Explorer le catalogue
            </a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features">
    <div class="features-container">
        <div class="section-header">
            <h2 class="section-title">Pourquoi choisir Streamy ?</h2>
            <p class="section-subtitle">Une plateforme conçue pour les passionnés de cinéma</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-film"></i>
                </div>
                <h3 class="feature-title">Contenu illimité</h3>
                <p class="feature-text">Accédez à des milliers de films et séries, des classiques aux dernières nouveautés.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-tv"></i>
                </div>
                <h3 class="feature-title">Multi-plateforme</h3>
                <p class="feature-text">Regardez sur tous vos appareils : ordinateur, tablette, smartphone.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-heart"></i>
                </div>
                <h3 class="feature-title">Listes personnalisées</h3>
                <p class="feature-text">Créez vos listes de favoris et retrouvez vos contenus préférés.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <h3 class="feature-title">Regarder plus tard</h3>
                <p class="feature-text">Sauvegardez ce que vous voulez voir et reprenez où vous vous êtes arrêté.</p>
            </div>
        </div>
    </div>
</section>

<!-- Popular Movies Section -->
<section class="popular">
    <div class="section-header">
        <h2 class="section-title">Populaires cette semaine</h2>
        <p class="section-subtitle">Les films et séries les plus regardés</p>
    </div>
    
    <div class="popular-grid">
        <div class="popular-item" onclick="window.location.href='browse.php'">
            <img src="https://image.tmdb.org/t/p/w500/9gk7adHYeDvHkCSEqAvQNLV5Uc4.jpg" alt="Inception">
            <div class="popular-overlay">
                <div class="popular-title">Inception</div>
                <div class="popular-rating">⭐ 8.8/10</div>
            </div>
        </div>
        
        <div class="popular-item" onclick="window.location.href='browse.php'">
            <img src="https://image.tmdb.org/t/p/w500/qJ2tW6WMUDux911r6m7haRef0WH.jpg" alt="The Dark Knight">
            <div class="popular-overlay">
                <div class="popular-title">The Dark Knight</div>
                <div class="popular-rating">⭐ 9.0/10</div>
            </div>
        </div>
        
        <div class="popular-item" onclick="window.location.href='browse.php'">
            <img src="https://image.tmdb.org/t/p/w500/ggFHVNu6YYI5L9pCfOacjizRGt.jpg" alt="Breaking Bad">
            <div class="popular-overlay">
                <div class="popular-title">Breaking Bad</div>
                <div class="popular-rating">⭐ 9.5/10</div>
            </div>
        </div>
        
        <div class="popular-item" onclick="window.location.href='browse.php'">
            <img src="https://image.tmdb.org/t/p/w500/49WJfeN0moxb9IPfGn8AIqMGskD.jpg" alt="Stranger Things">
            <div class="popular-overlay">
                <div class="popular-title">Stranger Things</div>
                <div class="popular-rating">⭐ 8.7/10</div>
            </div>
        </div>
        
        <div class="popular-item" onclick="window.location.href='browse.php'">
            <img src="https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg" alt="Interstellar">
            <div class="popular-overlay">
                <div class="popular-title">Interstellar</div>
                <div class="popular-rating">⭐ 8.6/10</div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <h2 class="cta-title">Prêt à commencer votre aventure ?</h2>
    <p class="cta-text">Rejoignez Streamy aujourd'hui et profitez du meilleur du divertissement.</p>
    <a href="signup.php" class="btn btn-cta">
        <i class="fa-solid fa-user-plus"></i> Créer un compte gratuit
    </a>
</section>

<footer class="footer">
    <div class="footer-logo">Stream<span>y</span></div>
    <div style="display: flex; gap: 20px; flex-wrap: wrap; justify-content: center;">
        <a href="#" style="color: var(--muted); text-decoration: none;">À propos</a>
        <a href="#" style="color: var(--muted); text-decoration: none;">Conditions d'utilisation</a>
        <a href="#" style="color: var(--muted); text-decoration: none;">Confidentialité</a>
        <a href="#" style="color: var(--muted); text-decoration: none;">Contact</a>
    </div>
    <span>© 2025 Streamy. Tous droits réservés.</span>
</footer>

</body>
</html>