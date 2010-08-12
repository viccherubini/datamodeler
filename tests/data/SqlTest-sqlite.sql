DROP TABLE IF EXISTS products;
CREATE TABLE IF NOT EXISTS products (product_id integer PRIMARY KEY, date_created TEXT NOT NULL, date_updated TEXT DEFAULT NULL, date_available TEXT NOT NULL, customer_id INTEGER NOT NULL, price REAL NOT NULL, `name` TEXT NOT NULL, sku TEXT NOT NULL, field TEXT NOT NULL);
INSERT INTO products VALUES(1, '0000-00-00 00:00:00', NULL, '0000-00-00', 0, 0, 'Product 1', 'P1', '');
INSERT INTO products VALUES(2, '0000-00-00 00:00:00', NULL, '0000-00-00', 0, 0, 'Product 2', 'P2', '');
INSERT INTO products VALUES(3, '0000-00-00 00:00:00', NULL, '0000-00-00', 0, 0, 'Product 3', 'P3', '');


DROP TABLE IF EXISTS users;
CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT, password TEXT, age INTEGER, favorite_book TEXT);
INSERT INTO users VALUES(NULL, 'vcherubini', 'password1', 25, 'xUnit Test Patterns');
INSERT INTO users VALUES(NULL, 'bsaget', 'password2', 50, 'The Olsen Twins: A Legacy');
INSERT INTO users VALUES(NULL, 'ggottfried', 'password3', 52, 'Its The Whiskey Talkin');
INSERT INTO users VALUES(NULL, 'howard_stern', 'password4', 56, 'Private Parts');

DROP TABLE IF EXISTS large_object;
CREATE TABLE large_object (large_object_id INTEGER PRIMARY KEY, object_data TEXT);

DROP TABLE IF EXISTS orders;
CREATE TABLE orders (order_id INTEGER PRIMARY KEY, date_created TEXT, date_updated TEXT NULL DEFAULT NULL, date_available TEXT, customer_id INTEGER, total REAL, name TEXT);