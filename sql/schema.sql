CREATE DATABASE IF NOT EXISTS event_management;

USE event_management;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    date DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS participations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    participation_fee DECIMAL(10, 2) NOT NULL,
    event_date DATETIME NOT NULL,
    version VARCHAR(20),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (event_id) REFERENCES events(id)
);

CREATE INDEX idx_event_date ON events (date);
CREATE INDEX idx_user_name ON users (name);
CREATE INDEX idx_event_name ON events (name);
