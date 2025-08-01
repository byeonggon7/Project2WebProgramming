CREATE DATABASE IF NOT EXISTS fifteen_puzzle;
USE fifteen_puzzle;

-- USERS TABLE
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('player', 'admin') NOT NULL DEFAULT 'player',
    registration_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME
);

-- GAME STATS TABLE
CREATE TABLE IF NOT EXISTS game_stats (
    stat_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    puzzle_size VARCHAR(10) NOT NULL,
    time_taken_seconds INT NOT NULL,
    moves_count INT NOT NULL,
    background_image_id INT,
    win_status BOOLEAN NOT NULL,
    game_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (background_image_id) REFERENCES background_images(image_id)
);

-- USER PREFERENCES TABLE
CREATE TABLE IF NOT EXISTS user_preferences (
    preference_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    default_puzzle_size VARCHAR(10) DEFAULT '4x4',
    preferred_background_image_id INT,
    sound_enabled BOOLEAN DEFAULT TRUE,
    animations_enabled BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (preferred_background_image_id) REFERENCES background_images(image_id)
);

-- BACKGROUND IMAGES TABLE
CREATE TABLE IF NOT EXISTS background_images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    image_name VARCHAR(100) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    uploaded_by_user_id INT,
    FOREIGN KEY (uploaded_by_user_id) REFERENCES users(user_id)
);
