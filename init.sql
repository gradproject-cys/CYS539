CREATE DATABASE IF NOT EXISTS xsslab;
USE xsslab;

CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    role       ENUM('user','admin') DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS posts (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    title      VARCHAR(200) NOT NULL,
    body       TEXT NOT NULL,
    author_id  INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS comments (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    post_id    INT NOT NULL,
    author     VARCHAR(100) NOT NULL,
    body       TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS search_log (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    query       TEXT NOT NULL,
    searched_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Admin password: Admin@1234
INSERT IGNORE INTO users (username, password, role) VALUES
  ('admin', '$2y$12$UNcnQZx4Ma1Ik4KDSVqNEeMgkWj4Ic685i.Ux6PXuONhRNsqO11q.', 'admin');

-- User password: User@1234
INSERT IGNORE INTO users (username, password, role) VALUES
  ('Monther', '$2y$12$WfZ/BDk/1iHJ2jGDkEIjVOajZICFxgZXZ7m0WOsoDLs9MOCZEuadW', 'user');

INSERT IGNORE INTO posts (id, title, body, author_id) VALUES
(1, 'Al-Ula: Where History Meets the Desert Sky',
 'There is a particular quality of silence in the desert at dusk. Standing among the Nabataean tombs of Hegra, watching sandstone cliffs turn amber as the sun drops, is an experience that stays with you. Al-Ula recalibrates your sense of scale — vast valleys, ancient ruins, and a sky so wide it feels personal.',
 1),
(2, 'A Weekend in Jeddah''s Al-Balad District',
 'The coral-stone houses, the smell of oud drifting from open doorways, the sound of the call to prayer echoing through narrow alleyways. Old Jeddah is a UNESCO World Heritage Site, and once you are inside it, you understand why. I spent two days getting happily lost, eating the best ful medames of my life near Bab Makkah.',
 1),
(3, 'The Best Kabsa in Riyadh — A Personal Ranking',
 'After three years of eating kabsa at every restaurant I could find in Riyadh, I have developed strong opinions. The rice must be fragrant with dried lemon and whole spices. The meat should fall off the bone. The dipping sauce is non-negotiable. Here is where to go — and more importantly, where not to.',
 1);
