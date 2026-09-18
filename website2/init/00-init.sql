CREATE DATABASE IF NOT EXISTS janus_website2;
USE janus_website2;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
);

INSERT INTO users (username, password) VALUES
    ('user1', 'd9a4c92a4f14364ebc9954de879b82b189ebb015b99f331f4efdb640ac0173b5'),
    ('user2', '00982f33c01c0906489dd24a21240c1fc2a14ca28034105bfb7b42466e49c18a'),
    ('admin', 'c1e1becc3fe5bcb7c3ee85a493c6e004e455584869841265c69f72c132b2a991');
