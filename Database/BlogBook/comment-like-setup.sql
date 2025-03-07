CREATE TABLE IF NOT EXISTS comment_like (
  userID INT,
  commentID INT,
  FOREIGN KEY (userID) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (commentID) REFERENCES comments(id) ON DELETE CASCADE,
  PRIMARY KEY (userID, commentID)
) 