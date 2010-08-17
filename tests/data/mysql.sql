DROP TABLE IF EXISTS products;
CREATE TABLE IF NOT EXISTS products (product_id int(11) NOT NULL AUTO_INCREMENT, date_created datetime NOT NULL, date_updated datetime DEFAULT NULL, date_available date NOT NULL, customer_id int(11) NOT NULL, price float NOT NULL, `name` varchar(255) NOT NULL, sku varchar(12) NOT NULL, field text NOT NULL, PRIMARY KEY (product_id)) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE products CHANGE field field TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
INSERT INTO products VALUES(1, '0000-00-00 00:00:00', NULL, '0000-00-00', 0, 0, 'Product 1', 'P1', '');
INSERT INTO products VALUES(2, '0000-00-00 00:00:00', NULL, '0000-00-00', 0, 0, 'Product 2', 'P2', '');
INSERT INTO products VALUES(3, '0000-00-00 00:00:00', NULL, '0000-00-00', 0, 0, 'Product 3', 'P3', '');

DROP TABLE IF EXISTS users;
CREATE TABLE users (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, age INT NOT NULL, favorite_book VARCHAR(255) NOT NULL) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;
INSERT INTO users VALUES(NULL, 'vcherubini', 'password1', 25, 'xUnit Test Patterns');
INSERT INTO users VALUES(NULL, 'bsaget', 'password2', 50, 'The Olsen Twins: A Legacy');
INSERT INTO users VALUES(NULL, 'ggottfried', 'password3', 52, 'Its The Whiskey Talkin');
INSERT INTO users VALUES(NULL, 'howard_stern', 'password4', 56, 'Private Parts');

DROP TABLE IF EXISTS large_object;
CREATE TABLE large_object (large_object_id SMALLINT(3) NOT NULL AUTO_INCREMENT PRIMARY KEY, object_data LONGBLOB NOT NULL) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;

DROP TABLE IF EXISTS orders;
CREATE TABLE orders (order_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, date_created DATETIME NOT NULL, date_updated DATETIME NULL DEFAULT NULL, date_available DATE NOT NULL, customer_id INT NOT NULL, total DOUBLE NOT NULL, name VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;