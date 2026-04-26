# 🎬 Streamy — Setup Guide

## Requirements
- PHP 7.4+ (XAMPP / WAMP / Laragon)
- MySQL 5.7+
- Web server (Apache)

---

## Installation (5 steps)

### 1. Copy the folder
Put the `streamy/` folder inside your web root:
- XAMPP → `C:/xampp/htdocs/streamy/`
- WAMP  → `C:/wamp64/www/streamy/`
- Laragon → `C:/laragon/www/streamy/`

### 2. Create the database
Open **phpMyAdmin** → click "New" → create database named `streamy`
Then go to the SQL tab and paste the contents of `database/streamy.sql`, click **Go**.

(Or run: `mysql -u root -p streamy < database/streamy.sql`)

### 3. Configure the connection
Open `database/config.php` and check:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');     // your MySQL user
define('DB_PASS', '');         // your MySQL password
define('DB_NAME', 'streamy');
```

### 4. Start your server
- XAMPP: start Apache + MySQL in the control panel
- Laragon: click Start All

### 5. Open in browser
`http://localhost/streamy/`

---

## Pages

| URL | Description |
|-----|-------------|
| `/` or `index.html` | Landing page (public) |
| `/signin.php` | Login |
| `/signup.php` | Register |
| `/browse.php` | Home (logged in) — movies, series, filters |
| `/movie.php?id=X` | Movie/Series detail, trailer, episodes |
| `/profile.php` | Favorites, Watch Later, History, Account |

---

## How to add more movies/series

Open **phpMyAdmin** → `streamy` database → `media` table → click "Insert".

Fill in:
- `title` — Title
- `type` — `movie` or `series`
- `category` — genre key: `action`, `comedy`, `horror`, `anime`, `kids`, `nostalgie`, `scifi`, `drama`
- `genre` — display label e.g. `"Action / Thriller"`
- `description` — synopsis
- `release_year` — e.g. `2023`
- `rating` — e.g. `8.4`
- `image_url` — poster URL (use TMDB: `https://image.tmdb.org/t/p/w500/POSTER_PATH.jpg`)
- `backdrop_url` — wide background image URL
- `trailer_url` — YouTube embed URL e.g. `https://www.youtube.com/embed/VIDEO_ID`
- `director` — director name
- `cast_list` — main actors
- `seasons` — (series only) number of seasons
- `episodes` — (series only) total episode count

For **episodes**, insert into the `episodes` table:
- `media_id` — ID of the series
- `season_number`, `episode_number`
- `title`, `description`, `duration` (in minutes)
- `thumbnail_url` — episode thumbnail

---

## File structure

```
streamy/
├── index.html          ← Landing page
├── signin.php          ← Sign in
├── signup.php          ← Sign up
├── browse.php          ← Home / catalog
├── movie.php           ← Film or series detail
├── profile.php         ← User profile
├── streamy.css         ← Global styles
└── database/
    ├── config.php           ← DB connection + helpers
    ├── streamy.sql          ← Schema + seed data (run once)
    ├── signin.php           ← Auth handler
    ├── signup.php           ← Register handler
    ├── logout.php           ← Logout handler
    ├── toggle_favorite.php  ← Add/remove favorites
    ├── toggle_watchlater.php← Add/remove watch later
    ├── clear_history.php    ← Clear all history
    └── remove_history_item.php ← Remove one item
```

---

## Find poster images (TMDB)

1. Go to https://www.themoviedb.org
2. Search for any movie/series
3. Right-click the poster → "Copy image address"
4. Replace `/w500/` if needed for different sizes

---

Enjoy Streamy 🎬
