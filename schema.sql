CREATE DATABASE IF NOT EXISTS contact_manager_app;

USE contact_manager_app;

CREATE TABLE IF NOT EXISTS Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS Contacts (
    contact_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone_number VARCHAR(20) NOT NULL DEFAULT '',
    email VARCHAR(255) NOT NULL DEFAULT '',
    notes VARCHAR(512) NOT NULL DEFAULT '',
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE,

    INDEX user_index (user_id),
    name_search TEXT GENERATED ALWAYS AS (LOWER(CONCAT(first_name, ' ', last_name))) STORED,
    FULLTEXT INDEX name_bigram (name_search) WITH PARSER ngram
);