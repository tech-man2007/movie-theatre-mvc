CREATE DATABASE movie_theatre;
USE movie_theatre;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    duration INT NOT NULL,
    language VARCHAR(50) NOT NULL
);

INSERT INTO movies (title, duration, language) VALUES 
('Kaithi', 145, 'Tamil'),
('Vikram', 173, 'Tamil'),
('Leo', 164, 'Tamil');
