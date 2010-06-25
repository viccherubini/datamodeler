DROP TABLE IF EXISTS products;
CREATE TABLE products (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL, price FLOAT NOT NULL, sku VARCHAR(12) NOT NULL) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;
INSERT INTO products VALUES (NULL, 'Product 1', 10.95, 'P1');
INSERT INTO products VALUES (NULL, 'Product 2', 18.95, 'P2');
INSERT INTO products VALUES (NULL, 'Product 3', 22.97, 'P3');

DROP TABLE IF EXISTS users;
CREATE TABLE users (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, age INT NOT NULL, favorite_book VARCHAR(255) NOT NULL) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;
INSERT INTO users VALUES(NULL, 'vcherubini', 'password1', 25, 'xUnit Test Patterns');
INSERT INTO users VALUES(NULL, 'bsaget', 'password2', 50, 'The Olsen Twins: A Legacy');
INSERT INTO users VALUES(NULL, 'ggottfried', 'password3', 52, 'Its The Whiskey Talkin');
INSERT INTO users VALUES(NULL, 'howard_stern', 'password4', 56, 'Private Parts');

DROP TABLE IF EXISTS large_object;
CREATE TABLE large_object (large_object_id SMALLINT(3) NOT NULL AUTO_INCREMENT PRIMARY KEY, object_data LONGTEXT NOT NULL) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;

DROP TABLE IF EXISTS orders;
CREATE TABLE orders (order_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, date_created DATETIME NOT NULL, date_updated DATETIME NOT NULL, customer_id INT NOT NULL, name VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE orders CHANGE date_updated date_updated DATETIME NULL DEFAULT NULL;