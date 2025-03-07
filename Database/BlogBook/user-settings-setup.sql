CREATE TABLE IF NOT EXISTS user_settings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  userID INT NOT NULL,
  metaKey VARCHAR(255) NOT NULL,
  metaValue TEXT NOT NULL,
  FOREIGN KEY (userID) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE (userID, metaKey)
)