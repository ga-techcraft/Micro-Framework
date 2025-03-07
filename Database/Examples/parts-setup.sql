CREATE TABLE IF NOT EXISTS parts (
  id INT PRIMARY KEY AUTO_INCREMENT,
  carID INT,
  name VARCHAR(255),
  description TEXT,
  price FLOAT,
  quantityInStock INT,
  FOREIGN KEY (carID) REFERENCES cars(id)
);