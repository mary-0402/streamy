# ═══════════════════════════════════════════════════════
#  HOW TO ADD YOUR OWN MOVIES & SERIES — Streamy Guide
# ═══════════════════════════════════════════════════════

## Step 1 — Add your poster image

Put your image file in:   streamy/assets/images/

Naming tip:  use lowercase with hyphens, e.g.
  my-movie.jpg
  breaking-good.jpg


## Step 2 — Get your trailer embed URL

1. Go to YouTube and find the official trailer
2. Click Share → Embed
3. Copy the URL from src="..."
   It looks like: https://www.youtube.com/embed/ABC123xyz

   ⚠️ Do NOT use the regular watch URL (youtube.com/watch?v=...)
   ✅ Use the EMBED URL (youtube.com/embed/...)


## Step 3 — Open phpMyAdmin

Go to:  http://localhost/phpmyadmin
Select the "streamy" database
Click the "SQL" tab
Paste and run one of the queries below


## ─── Add a MOVIE ──────────────────────────────────────

INSERT INTO media
  (title, type, genre, release_year, rating, description,
   image_url, backdrop_url, trailer_url,
   episodes, seasons, director, cast_list)
VALUES
  (
    'My Movie Title',         -- Title
    'movie',                  -- 'movie' or 'series'
    'Action',                 -- Genre: Action, Drama, Sci-Fi, Crime, Fantasy, Comedy, Thriller...
    2024,                     -- Release year
    8.2,                      -- Rating out of 10 (e.g. 7.5)
    'A short description of the movie.',
    'assets/images/my-movie.jpg',         -- Your poster image
    'assets/images/my-movie-bg.jpg',      -- Wide backdrop (can be same as poster)
    'https://www.youtube.com/embed/XXXXX', -- YouTube embed URL
    NULL,                     -- Episodes: NULL for movies
    NULL,                     -- Seasons:  NULL for movies
    'Director Full Name',
    'Actor 1, Actor 2, Actor 3'
  );


## ─── Add a SERIES ─────────────────────────────────────

INSERT INTO media
  (title, type, genre, release_year, rating, description,
   image_url, backdrop_url, trailer_url,
   episodes, seasons, director, cast_list)
VALUES
  (
    'My Series Title',
    'series',                 -- must be 'series'
    'Drama',
    2023,
    9.0,
    'Description of the series.',
    'assets/images/my-series.jpg',
    'assets/images/my-series-bg.jpg',
    'https://www.youtube.com/embed/YYYYY',
    24,                       -- Total number of episodes
    2,                        -- Number of seasons
    'Creator Name',
    'Lead Actor, Supporting Actor'
  );


## ─── Use an online image (TMDB) ───────────────────────

If you don't have a local image, you can use TMDB poster URLs:
  https://image.tmdb.org/t/p/w500/POSTER_HASH.jpg   (poster)
  https://image.tmdb.org/t/p/original/BG_HASH.jpg   (backdrop)

Find them at: https://www.themoviedb.org — search your movie, 
right-click the poster → "Copy image address"


## ─── Edit an existing title ──────────────────────────

UPDATE media
SET rating = 9.1,
    description = 'Updated description.'
WHERE title = 'Breaking Bad';


## ─── Delete a title ───────────────────────────────────

DELETE FROM media WHERE title = 'My Movie Title';


## ─── View all titles ──────────────────────────────────

SELECT id, title, type, genre, release_year, rating FROM media ORDER BY id;


# ═══════════════════════════════════════════════════════
#  FILE STRUCTURE
# ═══════════════════════════════════════════════════════

streamy/
├── index.php               ← Landing page (public)
├── signin.php              ← Sign In form
├── signup.php              ← Sign Up form
├── browse.php              ← Main catalog (requires login)
├── movie.php               ← Movie/Series detail page
├── favorites.php           ← User's saved list
├── streamy.css             ← All styles
├── assets/
│   └── images/             ← Put YOUR posters here
└── database/
    ├── config.php          ← DB credentials (edit this!)
    ├── signup.php          ← Handles register form
    ├── signin.php          ← Handles login form
    ├── logout.php          ← Destroys session
    ├── toggle_favorite.php ← Add/remove favorites (fetch API)
    └── streamy.sql         ← Run this ONCE to create tables


# ═══════════════════════════════════════════════════════
#  SETUP CHECKLIST
# ═══════════════════════════════════════════════════════

[ ] 1. Copy the "streamy" folder to C:/xampp/htdocs/
[ ] 2. Open phpMyAdmin → Import → select database/streamy.sql
[ ] 3. Open database/config.php and check DB_USER / DB_PASS
[ ] 4. Visit http://localhost/streamy/
[ ] 5. Create an account and start browsing!
