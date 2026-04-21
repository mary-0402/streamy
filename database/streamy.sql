-- Supprimer l'ancienne base de données
DROP DATABASE IF EXISTS streamy;

-- Créer la nouvelle base de données simplifiée
CREATE DATABASE streamy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE streamy;

-- Table users (un seul compte principal)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Table media (avec catégories)
CREATE TABLE IF NOT EXISTS media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    type ENUM('movie', 'series') NOT NULL DEFAULT 'movie',
    genre VARCHAR(100),
    category VARCHAR(50) DEFAULT 'general', -- anime, kids, horror, comedy, general
    release_year INT,
    rating DECIMAL(3, 1),
    description TEXT,
    image_url VARCHAR(500),
    backdrop_url VARCHAR(500),
    trailer_url VARCHAR(500),
    episodes INT DEFAULT NULL,
    seasons INT DEFAULT NULL,
    director VARCHAR(150),
    cast_list VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table episodes (pour les séries)
CREATE TABLE IF NOT EXISTS episodes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    media_id INT NOT NULL,
    season_number INT NOT NULL,
    episode_number INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    duration INT,
    video_url VARCHAR(500),
    thumbnail_url VARCHAR(500),
    air_date DATE,
    FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE CASCADE,
    UNIQUE KEY unique_episode (
        media_id,
        season_number,
        episode_number
    )
);

-- Table favorites
CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    media_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE CASCADE,
    UNIQUE KEY unique_fav (user_id, media_id)
);

-- Table watch_later
CREATE TABLE IF NOT EXISTS watch_later (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    media_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE CASCADE,
    UNIQUE KEY unique_watch_later (user_id, media_id)
);

-- Table watch_history
CREATE TABLE IF NOT EXISTS watch_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    media_id INT NOT NULL,
    episode_id INT NULL,
    progress INT DEFAULT 0,
    watched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE CASCADE,
    FOREIGN KEY (episode_id) REFERENCES episodes (id) ON DELETE SET NULL
);

-- Créer un compte utilisateur principal (email: admin@streamy.com, password: admin123)
INSERT INTO
    users (
        username,
        email,
        password,
        created_at
    )
VALUES (
        'admin',
        'admin@streamy.com',
        SHA2('admin123', 256),
        NOW()
    );

-- Insérer les médias avec catégories
INSERT INTO
    media (
        title,
        type,
        genre,
        category,
        release_year,
        rating,
        description,
        image_url,
        backdrop_url,
        trailer_url,
        episodes,
        seasons,
        director,
        cast_list
    )
VALUES
    -- Movies
    (
        'Inception',
        'movie',
        'Sci-Fi',
        'general',
        2010,
        8.8,
        'A thief who enters the dreamscape of others to steal secrets is given a reverse task: plant an idea.',
        'https://image.tmdb.org/t/p/w500/9gk7adHYeDvHkCSEqAvQNLV5Uc4.jpg',
        'https://image.tmdb.org/t/p/original/s3TBrRGB1iav7gFOCNx3H31HES0.jpg',
        'https://www.youtube.com/embed/YoHD9XEInc0',
        NULL,
        NULL,
        'Christopher Nolan',
        'Leonardo DiCaprio, Joseph Gordon-Levitt, Elliot Page'
    ),
    (
        'The Dark Knight',
        'movie',
        'Action',
        'general',
        2008,
        9.0,
        'Batman faces the Joker, a criminal mastermind who plunges Gotham into anarchy.',
        'https://image.tmdb.org/t/p/w500/qJ2tW6WMUDux911r6m7haRef0WH.jpg',
        'https://image.tmdb.org/t/p/original/nMKd7epw4DiwQrC2G5bA1rU6k6T.jpg',
        'https://www.youtube.com/embed/EXeTwQWrcwY',
        NULL,
        NULL,
        'Christopher Nolan',
        'Christian Bale, Heath Ledger, Aaron Eckhart'
    ),
    (
        'Interstellar',
        'movie',
        'Sci-Fi',
        'general',
        2014,
        8.6,
        'A team of explorers travel through a wormhole in space to ensure humanitys survival.',
        'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg',
        'https://image.tmdb.org/t/p/original/xu9zaAevzQ5nnrsXN6JcahLnG4i.jpg',
        'https://www.youtube.com/embed/zSWdZVtXT7E',
        NULL,
        NULL,
        'Christopher Nolan',
        'Matthew McConaughey, Anne Hathaway, Jessica Chastain'
    ),
    (
        'The Conjuring',
        'movie',
        'Horror',
        'horror',
        2013,
        7.5,
        'Paranormal investigators help a family terrorized by a dark presence.',
        'https://image.tmdb.org/t/p/w500/xfGiI0mQq8Pb5wYCH6zL3nZ5pD2.jpg',
        'https://image.tmdb.org/t/p/original/wVYzG1fMqZrB6Nfq6VX2tR4Q8Yk.jpg',
        'https://www.youtube.com/embed/k10ETZ41q5o',
        NULL,
        NULL,
        'James Wan',
        'Vera Farmiga, Patrick Wilson'
    ),
    (
        'Spirited Away',
        'movie',
        'Fantasy',
        'anime',
        2001,
        8.6,
        'A young girl enters a spirit world and must work to save her parents.',
        'https://image.tmdb.org/t/p/w500/39wmItIWsg5sZMyRUHLkWBcuVCM.jpg',
        'https://image.tmdb.org/t/p/original/bSXfU4dwZyBA1vMmXvejdRX1vuJ.jpg',
        'https://www.youtube.com/embed/ByXuk9QqQkk',
        NULL,
        NULL,
        'Hayao Miyazaki',
        'Rumi Hiiragi, Miyu Irino'
    ),
    (
        'Toy Story',
        'movie',
        'Animation',
        'kids',
        1995,
        8.3,
        'A cowboy doll is threatened when a new spaceman action figure arrives.',
        'https://image.tmdb.org/t/p/w500/uXDfjJbdP4ijW5hWSBrPrlKpxab.jpg',
        'https://image.tmdb.org/t/p/original/wHtXjKpxZfC9nFyC9qUeK3ZQ7cY.jpg',
        'https://www.youtube.com/embed/v-PjgYDrg70',
        NULL,
        NULL,
        'John Lasseter',
        'Tom Hanks, Tim Allen'
    ),
    (
        'Superbad',
        'movie',
        'Comedy',
        'comedy',
        2007,
        7.6,
        'Two co-dependent high school seniors try to score alcohol for a party.',
        'https://image.tmdb.org/t/p/w500/ek8e8txUyUwdQj2OZUzPz7F0Z6K.jpg',
        'https://image.tmdb.org/t/p/original/6YOpUY7VhKvC7Y5wJ2yYKz8t1cJ.jpg',
        'https://www.youtube.com/embed/4XrK2pQfQzY',
        NULL,
        NULL,
        'Greg Mottola',
        'Jonah Hill, Michael Cera'
    ),
    (
        'Oppenheimer',
        'movie',
        'Drama',
        'general',
        2023,
        8.5,
        'The story of American scientist J. Robert Oppenheimer and the atomic bomb.',
        'https://image.tmdb.org/t/p/w500/8Gxv8gSFCU0XGDykEGv7zR1n2ua.jpg',
        'https://image.tmdb.org/t/p/original/rLb2cwF3Pazuxaj0sRXQ037tGI1.jpg',
        'https://www.youtube.com/embed/bK6ldnjE3Y0',
        NULL,
        NULL,
        'Christopher Nolan',
        'Cillian Murphy, Emily Blunt, Robert Downey Jr.'
    ),
    (
        'The Godfather',
        'movie',
        'Crime',
        'general',
        1972,
        9.2,
        'The aging patriarch of a crime dynasty transfers control to his reluctant son.',
        'https://image.tmdb.org/t/p/w500/3bhkrj58Vtu7enYsRolD1fZdja1.jpg',
        'https://image.tmdb.org/t/p/original/tmU7GeKVybMWFPUcjc3V7t7Av5v.jpg',
        'https://www.youtube.com/embed/sY1S34973zA',
        NULL,
        NULL,
        'Francis Ford Coppola',
        'Marlon Brando, Al Pacino, James Caan'
    ),

-- Series
(
    'Breaking Bad',
    'series',
    'Drama',
    'general',
    2008,
    9.5,
    'A high-school chemistry teacher turned methamphetamine manufacturer.',
    'https://image.tmdb.org/t/p/w500/ggFHVNu6YYI5L9pCfOacjizRGt.jpg',
    'https://image.tmdb.org/t/p/original/ggFHVNu6YYI5L9pCfOacjizRGt.jpg',
    'https://www.youtube.com/embed/HhesaQXLuRY',
    62,
    5,
    'Vince Gilligan',
    'Bryan Cranston, Aaron Paul'
),
(
    'Stranger Things',
    'series',
    'Sci-Fi',
    'general',
    2016,
    8.7,
    'Kids uncover supernatural mysteries in their small town.',
    'https://image.tmdb.org/t/p/w500/49WJfeN0moxb9IPfGn8AIqMGskD.jpg',
    'https://image.tmdb.org/t/p/original/56v2KjBlU4XaOv9rVYEQypROD7P.jpg',
    'https://www.youtube.com/embed/b9EkMc79ZSU',
    34,
    4,
    'Duffer Brothers',
    'Millie Bobby Brown, Finn Wolfhard'
),
(
    'Attack on Titan',
    'series',
    'Action',
    'anime',
    2013,
    9.0,
    'Humanity fights for survival against giant humanoid creatures.',
    'https://image.tmdb.org/t/p/w500/r4eiAiY4wNZ9vMLjFZt0zO9sN8v.jpg',
    'https://image.tmdb.org/t/p/original/zXWJnPCz2CkL2q4sQq7YqYq8YqY.jpg',
    'https://www.youtube.com/embed/MGRm4IzK1SQ',
    87,
    4,
    'Hajime Isayama',
    'Yuki Kaji, Yui Ishikawa'
),
(
    'The Office',
    'series',
    'Comedy',
    'comedy',
    2005,
    8.9,
    'A mockumentary on a group of office workers.',
    'https://image.tmdb.org/t/p/w500/qWnJxZirh7BwG6QkWf8QN4QzYQc.jpg',
    'https://image.tmdb.org/t/p/original/8JcYQqYqYqYqYqYqYqYqYqYqYq.jpg',
    'https://www.youtube.com/embed/Lt6M0QYqYqY',
    201,
    9,
    'Greg Daniels',
    'Steve Carell, John Krasinski'
),
(
    'The Haunting of Hill House',
    'series',
    'Horror',
    'horror',
    2018,
    8.6,
    'A family confronts haunting memories of their old home.',
    'https://image.tmdb.org/t/p/w500/uwmYjYqYqYqYqYqYqYqYqYqYqYq.jpg',
    'https://image.tmdb.org/t/p/original/8JcYQqYqYqYqYqYqYqYqYqYqYq.jpg',
    'https://www.youtube.com/embed/G9YqYqYqYqY',
    10,
    1,
    'Mike Flanagan',
    'Michiel Huisman, Carla Gugino'
),
(
    'Peaky Blinders',
    'series',
    'Crime',
    'general',
    2013,
    8.8,
    'A gangster family in 1920s Birmingham expand their criminal empire.',
    'https://image.tmdb.org/t/p/w500/vUUqzWa2LnHIVqkaKVlVGkVcZLX.jpg',
    'https://image.tmdb.org/t/p/original/9QusWjfnYHIy5kYFBwMGibZ23G2.jpg',
    'https://www.youtube.com/embed/oVzVdvGIC7U',
    36,
    6,
    'Steven Knight',
    'Cillian Murphy, Tom Hardy, Helen McCrory'
);

-- Insérer les épisodes pour Breaking Bad (Saison 1)
INSERT INTO
    episodes (
        media_id,
        season_number,
        episode_number,
        title,
        description,
        duration,
        air_date
    )
SELECT id, 1, 1, 'Pilot', 'A high school chemistry teacher is diagnosed with cancer and turns to cooking meth.', 58, '2008-01-20'
FROM media
WHERE
    title = 'Breaking Bad'
LIMIT 1;

INSERT INTO
    episodes (
        media_id,
        season_number,
        episode_number,
        title,
        description,
        duration,
        air_date
    )
SELECT id, 1, 2, 'Cat''s in the Bag...', 'Walt and Jesse deal with two prisoners in their RV.', 48, '2008-01-27'
FROM media
WHERE
    title = 'Breaking Bad'
LIMIT 1;

INSERT INTO
    episodes (
        media_id,
        season_number,
        episode_number,
        title,
        description,
        duration,
        air_date
    )
SELECT id, 1, 3, '...And the Bag''s in the River', 'Walt must decide whether to kill a captive Krazy-8.', 48, '2008-02-10'
FROM media
WHERE
    title = 'Breaking Bad'
LIMIT 1;

INSERT INTO
    episodes (
        media_id,
        season_number,
        episode_number,
        title,
        description,
        duration,
        air_date
    )
SELECT id, 1, 4, 'Cancer Man', 'Walt breaks the news about his cancer to his family.', 48, '2008-02-17'
FROM media
WHERE
    title = 'Breaking Bad'
LIMIT 1;

INSERT INTO
    episodes (
        media_id,
        season_number,
        episode_number,
        title,
        description,
        duration,
        air_date
    )
SELECT id, 1, 5, 'Gray Matter', 'Walt attends a birthday party for a wealthy friend.', 48, '2008-02-24'
FROM media
WHERE
    title = 'Breaking Bad'
LIMIT 1;

INSERT INTO
    episodes (
        media_id,
        season_number,
        episode_number,
        title,
        description,
        duration,
        air_date
    )
SELECT id, 1, 6, 'Crazy Handful of Nothin''', 'Walt confronts a drug dealer using chemistry.', 48, '2008-03-02'
FROM media
WHERE
    title = 'Breaking Bad'
LIMIT 1;

INSERT INTO
    episodes (
        media_id,
        season_number,
        episode_number,
        title,
        description,
        duration,
        air_date
    )
SELECT id, 1, 7, 'A No-Rough-Stuff-Type Deal', 'Walt and Jesse find themselves in a dangerous situation.', 48, '2008-03-09'
FROM media
WHERE
    title = 'Breaking Bad'
LIMIT 1;