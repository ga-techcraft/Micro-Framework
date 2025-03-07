CREATE TABLE IF NOT EXISTS post_like (
  userID INT,
  postID INT,
  FOREIGN KEY (userID) REFERENCES users(id) ON DELETE CASCADE, 
  FOREIGN KEY (postID) REFERENCES posts(id) ON DELETE CASCADE, 
  PRIMARY KEY (userID, postID)
)