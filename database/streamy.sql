-- ═══════════════════════════════════════════
--  streamy.sql  —  Full schema + seed data
--  Run once: mysql -u root -p < streamy.sql
-- ═══════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS streamy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE streamy;

-- ── Users ──────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50)  NOT NULL,
    email       VARCHAR(100) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    created_at  DATETIME DEFAULT NOW(),
    last_login  DATETIME DEFAULT NULL
);

-- ── Media ──────────────────────────────────
CREATE TABLE IF NOT EXISTS media (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    title        VARCHAR(200) NOT NULL,
    type         ENUM('movie','series') NOT NULL,
    category     VARCHAR(50)  DEFAULT 'general',
    genre        VARCHAR(100) DEFAULT '',
    description  TEXT,
    release_year INT,
    rating       DECIMAL(3,1) DEFAULT 0,
    image_url    VARCHAR(500) DEFAULT '',
    backdrop_url VARCHAR(500) DEFAULT '',
    trailer_url  VARCHAR(500) DEFAULT '',
    director     VARCHAR(100) DEFAULT '',
    cast_list    VARCHAR(300) DEFAULT '',
    seasons      INT DEFAULT NULL,
    episodes     INT DEFAULT NULL
);

-- ── Episodes ───────────────────────────────
CREATE TABLE IF NOT EXISTS episodes (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    media_id       INT NOT NULL,
    season_number  INT NOT NULL DEFAULT 1,
    episode_number INT NOT NULL,
    title          VARCHAR(200),
    description    TEXT,
    duration       INT DEFAULT 45,
    thumbnail_url  VARCHAR(500) DEFAULT '',
    FOREIGN KEY (media_id) REFERENCES media(id) ON DELETE CASCADE
);

-- ── Favorites ──────────────────────────────
CREATE TABLE IF NOT EXISTS favorites (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    user_id   INT NOT NULL,
    media_id  INT NOT NULL,
    added_at  DATETIME DEFAULT NOW(),
    UNIQUE KEY uq_fav (user_id, media_id),
    FOREIGN KEY (user_id)  REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (media_id) REFERENCES media(id) ON DELETE CASCADE
);

-- ── Watch Later ────────────────────────────
CREATE TABLE IF NOT EXISTS watch_later (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    user_id   INT NOT NULL,
    media_id  INT NOT NULL,
    added_at  DATETIME DEFAULT NOW(),
    UNIQUE KEY uq_wl (user_id, media_id),
    FOREIGN KEY (user_id)  REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (media_id) REFERENCES media(id) ON DELETE CASCADE
);

-- ── Watch History ──────────────────────────
CREATE TABLE IF NOT EXISTS watch_history (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    media_id    INT NOT NULL,
    episode_id  INT DEFAULT NULL,
    watched_at  DATETIME DEFAULT NOW(),
    FOREIGN KEY (user_id)  REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (media_id) REFERENCES media(id) ON DELETE CASCADE
);

-- ═══════════════════════════════════════════
--  SEED DATA — 10 Movies + 5 Series
-- ═══════════════════════════════════════════

INSERT INTO media (title, type, category, genre, description, release_year, rating, image_url, backdrop_url, trailer_url, director, cast_list) VALUES

-- ── 10 MOVIES ──────────────────────────────
('Inception',
 'movie','scifi','Sci-Fi / Thriller',
 'A thief who steals corporate secrets through dream-sharing technology is given the inverse task of planting an idea into the mind of a C.E.O.',
 2010, 8.8,
 'https://image.tmdb.org/t/p/w500/9gk7adHYeDvHkCSEqAvQNLV5Uc4.jpg',
 'https://image.tmdb.org/t/p/original/s3TBrRGB1iav7gFOCNx3H31MoES.jpg',
 'https://www.youtube.com/embed/YoHD9XEInc0',
 'Christopher Nolan','Leonardo DiCaprio, Joseph Gordon-Levitt, Elliot Page'),

('The Dark Knight',
 'movie','action','Action / Crime',
 'Batman raises the stakes in his war on crime. With the help of Lt. Jim Gordon and DA Harvey Dent, he sets out to dismantle the remaining criminal organizations that plague Gotham.',
 2008, 9.0,
 'https://image.tmdb.org/t/p/w500/qJ2tW6WMUDux911r6m7haRef0WH.jpg',
 'https://image.tmdb.org/t/p/original/nMKdUUepR0i5zn0y1T4CejMioUE.jpg',
 'https://www.youtube.com/embed/EXeTwQWrcwY',
 'Christopher Nolan','Christian Bale, Heath Ledger, Aaron Eckhart'),

('Interstellar',
 'movie','scifi','Sci-Fi / Drama',
 'A team of explorers travel through a wormhole in space in an attempt to ensure humanity''s survival.',
 2014, 8.6,
 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg',
 'https://image.tmdb.org/t/p/original/pbrkL804c8yAv3zBZR4QPEafpAR.jpg',
 'https://www.youtube.com/embed/zSWdZVtXT7E',
 'Christopher Nolan','Matthew McConaughey, Anne Hathaway, Jessica Chastain'),

('Parasite',
 'movie','drama','Drama / Thriller',
 'Greed and class discrimination threaten the newly formed symbiotic relationship between the wealthy Park family and the destitute Kim clan.',
 2019, 8.5,
 'https://image.tmdb.org/t/p/w500/7IiTTgloJzvGI1TAYymCfbfl3vT.jpg',
 'https://image.tmdb.org/t/p/original/TU9NIjwzjoKPwQHoHshkFcQUCG.jpg',
 'https://www.youtube.com/embed/5xH0HfJHsaY',
 'Bong Joon-ho','Song Kang-ho, Lee Sun-kyun, Cho Yeo-jeong'),

('Pulp Fiction',
 'movie','nostalgie','Crime / Drama',
 'The lives of two mob hitmen, a boxer, a gangster and his wife intertwine in four tales of violence and redemption.',
 1994, 8.9,
 'https://image.tmdb.org/t/p/w500/d5iIlFn5s0ImszYzBPb8JPIfbXD.jpg',
 'https://image.tmdb.org/t/p/original/4cDFJr4HnXN5AdPw4AKrmLlMWdO.jpg',
 'https://www.youtube.com/embed/s7EdQ4FqbhY',
 'Quentin Tarantino','John Travolta, Samuel L. Jackson, Uma Thurman'),

('Avengers: Endgame',
 'movie','action','Action / Sci-Fi',
 'After the devastating events of Infinity War, the Avengers assemble once more to reverse Thanos'' actions and restore balance to the universe.',
 2019, 8.4,
 'https://image.tmdb.org/t/p/w500/or06FN3Dka5tukK1e9sl16pB3iy.jpg',
 'https://image.tmdb.org/t/p/original/7RyHsO4yDXtBv1zUU3mTpHeQ0d5.jpg',
 'https://www.youtube.com/embed/TcMBFSGVi1c',
 'Anthony & Joe Russo','Robert Downey Jr., Chris Evans, Mark Ruffalo'),

('The Lion King',
 'movie','nostalgie','Animation / Family',
 'Lion cub Simba idolises his father, King Mufasa, and takes to heart his own royal destiny. But not everyone in the kingdom celebrates the new heir''s arrival.',
 1994, 8.5,
 'https://image.tmdb.org/t/p/w500/sIGiLMQXjDEhFDRTXIPkxqKLPDj.jpg',
 'https://image.tmdb.org/t/p/original/wXsQvli6tWqja51pYxXNG1OLFYK.jpg',
 'https://www.youtube.com/embed/4sj1MT05lAA',
 'Roger Allers, Rob Minkoff','Matthew Broderick, Jeremy Irons, James Earl Jones'),

('Spirited Away',
 'movie','anime','Animation / Fantasy',
 'During her family''s move to the suburbs, a sullen 10-year-old girl wanders into a world ruled by gods, witches, and spirits where humans are changed into beasts.',
 2001, 8.6,
 'https://image.tmdb.org/t/p/w500/39wmItIWsg5sZMyRUHLkWBcuVCM.jpg',
 'https://image.tmdb.org/t/p/original/bSXfU4dwZyBA1vMmXvejdRXBvuF.jpg',
 'https://www.youtube.com/embed/ByXuk9QqQkk',
 'Hayao Miyazaki','Daveigh Chase, Suzanne Pleshette, Miyu Irino'),

('Get Out',
 'movie','horror','Horror / Thriller',
 'A young African-American visits his white girlfriend''s parents for the weekend, where his simmering uneasiness about their reception of him eventually reaches a boiling point.',
 2017, 7.7,
 'https://image.tmdb.org/t/p/w500/tFXcEccSQMf3lfhfXKSU9iRBpa3.jpg',
 'https://image.tmdb.org/t/p/original/mv3MTbsGkWJLRpDqGzGaqfHNt3e.jpg',
 'https://www.youtube.com/embed/DzfpyUB60YY',
 'Jordan Peele','Daniel Kaluuya, Allison Williams, Bradley Whitford'),

('Toy Story',
 'movie','kids','Animation / Family',
 'A cowboy doll is profoundly threatened and jealous when a new spaceman figure supplants him as top toy in a boy''s room.',
 1995, 8.3,
 'https://image.tmdb.org/t/p/w500/uXDfjJbdP4ijW5hWSBrPl9KyB47.jpg',
 'https://image.tmdb.org/t/p/original/vzmL6fP7aPKNKPRTFnZmiUfciyV.jpg',
 'https://www.youtube.com/embed/KYz2wyBy3kc',
 'John Lasseter','Tom Hanks, Tim Allen, Don Rickles'),

-- ── 5 SERIES ───────────────────────────────
('Breaking Bad',
 'series','drama','Crime / Drama',
 'A high school chemistry teacher turned methamphetamine manufacturer partners with a former student to secure his family''s financial future as he faces terminal lung cancer.',
 2008, 9.5,
 'https://image.tmdb.org/t/p/w500/ggFHVNu6YYI5L9pCfOacjizRGt.jpg',
 'https://image.tmdb.org/t/p/original/tsRy63Mu5cu8etL1X7ZLyf7UP1M.jpg',
 'https://www.youtube.com/embed/HhesaQXLuRY',
 'Vince Gilligan','Bryan Cranston, Aaron Paul, Anna Gunn'),

('Stranger Things',
 'series','scifi','Sci-Fi / Horror',
 'When a young boy disappears, his mother, a police chief and his friends must confront terrifying supernatural forces in order to get him back.',
 2016, 8.7,
 'https://image.tmdb.org/t/p/w500/49WJfeN0moxb9IPfGn8AIqMGskD.jpg',
 'https://image.tmdb.org/t/p/original/rcA17r3hfHtRrk3Xs3hXrgGeSGT.jpg',
 'https://www.youtube.com/embed/b9EkMc79ZSU',
 'The Duffer Brothers','Millie Bobby Brown, Finn Wolfhard, David Harbour'),

('Attack on Titan',
 'series','anime','Anime / Action',
 'After his hometown is destroyed and his mother killed, young Eren Yeager vows to cleanse the earth of the giant humanoid Titans that have brought humanity to the brink of extinction.',
 2013, 9.0,
 'https://image.tmdb.org/t/p/w500/hTP1DtLGFamjfu8WqjnuQdP1n4i.jpg',
 'https://image.tmdb.org/t/p/original/lFRkiRa9AxUkTQsH2Gp5l4QLNKt.jpg',
 'https://www.youtube.com/embed/MGRm4IzK1SQ',
 'Tetsuro Araki','Yuki Kaji, Marina Inoue, Yui Ishikawa'),

('The Office',
 'series','comedy','Comedy',
 'A mockumentary on a group of typical office workers, where the workday consists of ego clashes, inappropriate behavior, and tedium.',
 2005, 9.0,
 'https://image.tmdb.org/t/p/w500/7DJKHzAi83BmQrWLrYYOqcoKfhR.jpg',
 'https://image.tmdb.org/t/p/original/mLyW3UTgi2lsMdtCNGgQmJ68Lnf.jpg',
 'https://www.youtube.com/embed/LHOtME2DL4g',
 'Greg Daniels','Steve Carell, Rainn Wilson, John Krasinski'),

('Black Mirror',
 'series','scifi','Sci-Fi / Thriller',
 'An anthology series exploring a twisted, high-tech multiverse where humanity''s greatest innovations and darkest instincts collide.',
 2011, 8.8,
 'https://image.tmdb.org/t/p/w500/7PRddO7z7mcPi21nZTCMGShAyy1.jpg',
 'https://image.tmdb.org/t/p/original/kXfqcdQKsToO0OUXHcrrNCHDBzO.jpg',
 'https://www.youtube.com/embed/jDiYGjCLZfw',
 'Charlie Brooker','Various');

-- ── Set seasons/episodes counts ────────────
UPDATE media SET seasons=5, episodes=62 WHERE title='Breaking Bad';
UPDATE media SET seasons=4, episodes=34 WHERE title='Stranger Things';
UPDATE media SET seasons=4, episodes=87 WHERE title='Attack on Titan';
UPDATE media SET seasons=9, episodes=201 WHERE title='The Office';
UPDATE media SET seasons=6, episodes=27 WHERE title='Black Mirror';

-- ── Sample episodes for Breaking Bad ───────
INSERT INTO episodes (media_id, season_number, episode_number, title, description, duration, thumbnail_url) VALUES
((SELECT id FROM media WHERE title='Breaking Bad'), 1, 1, 'Pilot',
 'Mild-mannered chemistry teacher Walter White teams up with his former student Jesse Pinkman to cook meth after receiving a terminal cancer diagnosis.',
 58, 'https://image.tmdb.org/t/p/w500/ggFHVNu6YYI5L9pCfOacjizRGt.jpg'),
((SELECT id FROM media WHERE title='Breaking Bad'), 1, 2, 'Cat''s in the Bag',
 'Walt and Jesse try to dispose of the bodies of Emilio and Krazy-8, but things do not go as planned.',
 48, 'https://image.tmdb.org/t/p/w500/ggFHVNu6YYI5L9pCfOacjizRGt.jpg'),
((SELECT id FROM media WHERE title='Breaking Bad'), 1, 3, 'And the Bag''s in the River',
 'While cleaning up the lab, Walt finds a notebook with some notes that could expose him. Walt must decide what to do with Krazy-8.',
 48, 'https://image.tmdb.org/t/p/w500/ggFHVNu6YYI5L9pCfOacjizRGt.jpg'),
((SELECT id FROM media WHERE title='Breaking Bad'), 2, 1, 'Seven Thirty-Seven',
 'Walt and Jesse must raise $737,000 before a powerful drug lord discovers their operation.',
 47, 'https://image.tmdb.org/t/p/w500/ggFHVNu6YYI5L9pCfOacjizRGt.jpg'),
((SELECT id FROM media WHERE title='Breaking Bad'), 2, 2, 'Grilled',
 'Walt and Jesse are held hostage by Tuco. Hank intensifies his search for Walt.',
 47, 'https://image.tmdb.org/t/p/w500/ggFHVNu6YYI5L9pCfOacjizRGt.jpg');

-- ── Sample episodes for Stranger Things ────
INSERT INTO episodes (media_id, season_number, episode_number, title, description, duration, thumbnail_url) VALUES
((SELECT id FROM media WHERE title='Stranger Things'), 1, 1, 'The Vanishing of Will Byers',
 'On his way home from a friend''s house, young Will sees something terrifying. Nearby, a young girl with a shaved head appears.',
 47, 'https://image.tmdb.org/t/p/w500/49WJfeN0moxb9IPfGn8AIqMGskD.jpg'),
((SELECT id FROM media WHERE title='Stranger Things'), 1, 2, 'The Weirdo on Maple Street',
 'Lucas, Mike and Dustin try to talk to the girl they found in the woods. Eleven shows them something shocking.',
 55, 'https://image.tmdb.org/t/p/w500/49WJfeN0moxb9IPfGn8AIqMGskD.jpg'),
((SELECT id FROM media WHERE title='Stranger Things'), 1, 3, 'Holly, Jolly',
 'Joyce and Chief Hopper must decide whether or not they can trust each other to find Will.',
 51, 'https://image.tmdb.org/t/p/w500/49WJfeN0moxb9IPfGn8AIqMGskD.jpg'),
((SELECT id FROM media WHERE title='Stranger Things'), 2, 1, 'MadMax',
 'As the town preps for Halloween, a high-scoring unknown player has the boys rattled. Joyce and Hopper are called to the lab.',
 48, 'https://image.tmdb.org/t/p/w500/49WJfeN0moxb9IPfGn8AIqMGskD.jpg'),
((SELECT id FROM media WHERE title='Stranger Things'), 2, 2, 'Trick or Treat, Freak',
 'After a long night of trick-or-treating, Will feels the shadow monster''s grip tighten.',
 56, 'https://image.tmdb.org/t/p/w500/49WJfeN0moxb9IPfGn8AIqMGskD.jpg');

-- ── Sample episodes for Attack on Titan ────
INSERT INTO episodes (media_id, season_number, episode_number, title, description, duration, thumbnail_url) VALUES
((SELECT id FROM media WHERE title='Attack on Titan'), 1, 1, 'To You, in 2000 Years',
 'When the Colossal Titan breaches the outer wall, young Eren Yeager witnesses a tragedy that ignites his fierce determination.',
 24, 'https://image.tmdb.org/t/p/w500/hTP1DtLGFamjfu8WqjnuQdP1n4i.jpg'),
((SELECT id FROM media WHERE title='Attack on Titan'), 1, 2, 'That Day: The Fall of Shiganshina',
 'Eren and Mikasa struggle to survive in the aftermath of the Titans'' attack.',
 24, 'https://image.tmdb.org/t/p/w500/hTP1DtLGFamjfu8WqjnuQdP1n4i.jpg'),
((SELECT id FROM media WHERE title='Attack on Titan'), 1, 3, 'A Dim Light Amid Despair',
 'Eren and the others start their training to become soldiers.',
 24, 'https://image.tmdb.org/t/p/w500/hTP1DtLGFamjfu8WqjnuQdP1n4i.jpg');

-- ── Sample episodes for The Office ─────────
INSERT INTO episodes (media_id, season_number, episode_number, title, description, duration, thumbnail_url) VALUES
((SELECT id FROM media WHERE title='The Office'), 1, 1, 'Pilot',
 'The Scranton branch of Dunder Mifflin Paper Company gets a documentary film crew to follow them around for a day.',
 22, 'https://image.tmdb.org/t/p/w500/7DJKHzAi83BmQrWLrYYOqcoKfhR.jpg'),
((SELECT id FROM media WHERE title='The Office'), 1, 2, 'Diversity Day',
 'Michael holds a diversity seminar after an incident with a Chris Rock routine.',
 22, 'https://image.tmdb.org/t/p/w500/7DJKHzAi83BmQrWLrYYOqcoKfhR.jpg'),
((SELECT id FROM media WHERE title='The Office'), 2, 1, 'The Dundies',
 'Michael hosts the 9th Annual Dundies award ceremony at Chili''s.',
 22, 'https://image.tmdb.org/t/p/w500/7DJKHzAi83BmQrWLrYYOqcoKfhR.jpg');

-- ── Sample episodes for Black Mirror ───────
INSERT INTO episodes (media_id, season_number, episode_number, title, description, duration, thumbnail_url) VALUES
((SELECT id FROM media WHERE title='Black Mirror'), 1, 1, 'The National Anthem',
 'A controversial plot device forces a head of state into an extreme situation.',
 44, 'https://image.tmdb.org/t/p/w500/7PRddO7z7mcPi21nZTCMGShAyy1.jpg'),
((SELECT id FROM media WHERE title='Black Mirror'), 1, 2, 'Fifteen Million Merits',
 'In a world where people earn currency by cycling on exercise bikes, a man attempts to give a woman he likes a chance at fame.',
 62, 'https://image.tmdb.org/t/p/w500/7PRddO7z7mcPi21nZTCMGShAyy1.jpg'),
((SELECT id FROM media WHERE title='Black Mirror'), 3, 1, 'Nosedive',
 'A woman living in a world where people are rated by others on social media goes on a journey to improve her score.',
 63, 'https://image.tmdb.org/t/p/w500/7PRddO7z7mcPi21nZTCMGShAyy1.jpg');
