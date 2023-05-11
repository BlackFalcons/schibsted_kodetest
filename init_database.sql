CREATE DATABASE IF NOT EXISTS power_poster;
USE power_poster;

CREATE TABLE IF NOT EXISTS posts (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    Title VARCHAR(100),
    Content VARCHAR(2000),
    UserID CHAR(36),
    CreatedDate DATE DEFAULT CURRENT_DATE()
);

CREATE USER IF NOT EXISTS 'postmann'@'localhost' IDENTIFIED BY 'efI1qeBxNqAJcr-]';

GRANT SELECT, INSERT ON power_poster.posts TO 'postmann'@'localhost';

FLUSH PRIVILEGES;
