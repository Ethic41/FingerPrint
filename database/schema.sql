CREATE DATABASE IF NOT EXISTS fingerprint
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE fingerprint;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL,
    fullname VARCHAR(255) NOT NULL,
    indexfinger TEXT NULL,
    middlefinger TEXT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY users_username_unique (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
