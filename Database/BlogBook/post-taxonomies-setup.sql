CREATE TABLE IF NOT EXISTS post_taxonomies (
  id INT PRIMARY KEY AUTO_INCREMENT,
  postID INT NOT NULL,
  taxonomyID INT NOT NULL,
  FOREIGN KEY (postID) REFERENCES posts(id),
  FOREIGN KEY (taxonomyID) REFERENCES taxonomies(id),
  UNIQUE (postID, taxonomyID)
)