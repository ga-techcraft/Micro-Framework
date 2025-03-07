CREATE TABLE IF NOT EXISTS taxonomyTerms (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  taxonomyID INT NOT NULL,
  description VARCHAR(255) NOT NULL,
  parentTaxonomyTerm INT,
  FOREIGN KEY (taxonomyID) REFERENCES taxonomies(id),
  FOREIGN KEY (parentTaxonomyTerm) REFERENCES taxonomyTerms(id)
)