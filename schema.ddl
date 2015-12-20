DROP DATABASE IF EXISTS vacebook;
CREATE DATABASE vacebook;
USE vacebook;

CREATE TABLE users (
  id INT NOT NULL auto_increment,
  username VARCHAR(70) UNIQUE NOT NULL,
  password VARCHAR(50) NOT NULL,

  PRIMARY KEY(id)
);

CREATE TABLE posts (
  id INT NOT NULL auto_increment,
  user_id INT UNIQUE NOT NULL,
  body TEXT NOT NULL,

  PRIMARY KEY(id),
  FOREIGN KEY(user_id) REFERENCES users(id)
);
