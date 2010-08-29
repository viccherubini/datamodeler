DROP TABLE IF EXISTS products;
CREATE TABLE IF NOT EXISTS products (
	product_id int(11) NOT NULL AUTO_INCREMENT,
	date_created datetime NOT NULL,
	date_updated datetime DEFAULT NULL,
	date_available date DEFAULT NULL,
	`name` varchar(64) NOT NULL,
	price float NOT NULL DEFAULT 0.00,
	sku varchar(12) NOT NULL,
	description TEXT DEFAULT NULL,
	image varchar(128) DEFAULT NULL,
	available tinyint(1) default 0,
	store_name varchar(64) DEFAULT NULL,
	PRIMARY KEY (product_id)
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;
INSERT INTO products VALUES(1, NOW(), NULL, NOW(), 'Product 1', 19.33, 'SKU_P1', 'Product 1 Description', 'product1.jpg', 1, 'Costco');
INSERT INTO products VALUES(2, NOW(), NULL, NOW(), 'Product 2', 18.25, 'SKU_P2', 'Product 2 Description', 'product2.jpg', 1, 'Sams');
INSERT INTO products VALUES(3, NOW(), NULL, NOW(), 'Product 3', 17.96, 'SKU_P3', 'Product 3 Description', 'product3.jpg', 0, 'Wal-Mart');

DROP TABLE IF EXISTS users;
CREATE TABLE users (
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(255) NOT NULL,
	password VARCHAR(255) NOT NULL,
	age INT NOT NULL,
	favorite_book VARCHAR(255) DEFAULT NULL
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;
INSERT INTO users VALUES(NULL, 'vcherubini', 'password1', 25, 'xUnit Test Patterns');
INSERT INTO users VALUES(NULL, 'bsaget', 'password2', 50, 'The Olsen Twins: A Legacy');
INSERT INTO users VALUES(NULL, 'ggottfried', 'password3', 52, 'Its The Whiskey Talkin');
INSERT INTO users VALUES(NULL, 'howard_stern', 'password4', 56, 'Private Parts');

DROP TABLE IF EXISTS large_object;
CREATE TABLE large_object (
	large_object_id SMALLINT(3) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	object_data LONGBLOB NOT NULL
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;

DROP TABLE IF EXISTS orders;
CREATE TABLE orders (
	order_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	date_created DATETIME NOT NULL,
	date_updated DATETIME NULL DEFAULT NULL,
	date_available DATE NOT NULL,
	customer_id INT NOT NULL,
	total DOUBLE NOT NULL,
	name VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;