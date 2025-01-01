-- Create database
CREATE DATABASE IF NOT EXISTS your_database CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE your_database;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Exam table
CREATE TABLE exam (
    id INT PRIMARY KEY AUTO_INCREMENT,
    data JSON NOT NULL,
    duration INT NOT NULL,
    end_time DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- User responses table
CREATE TABLE user_answers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    exam_id INT NOT NULL,
    question_id INT NOT NULL,
    answer VARCHAR(1) NOT NULL,
    is_correct TINYINT(1) NOT NULL,
    exam_completed TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (exam_id) REFERENCES exam(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Indexes
CREATE INDEX idx_user_exam ON user_answers(user_id, exam_id);
CREATE INDEX idx_exam_completed ON user_answers(exam_completed);

INSERT INTO users (username, password) VALUES ('demo', '89e495e7941cf9e40e6980d14a16bf023ccd4c91'); -- pass: demo
