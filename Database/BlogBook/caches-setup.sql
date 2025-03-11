CREATE TABLE IF NOT EXISTS caches (
  id INT PRIMARY KEY AUTO_INCREMENT,
  cacheKey VARCHAR(255) NOT NULL,
  cacheValue TEXT NOT NULL,
  UNIQUE (cacheKey)
)

-- cacheKey
-- 例) bookSearch-title-{titleName}、bookSearch-isbn-{isbn}