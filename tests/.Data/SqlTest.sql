CREATE TABLE products (id INTEGER PRIMARY KEY, name TEXT, price REAL, sku TEXT);
INSERT INTO products VALUES (NULL, 'Product 1', 10.95, 'P1');
INSERT INTO products VALUES (NULL, 'Product 2', 18.95, 'P2');
INSERT INTO products VALUES (NULL, 'Product 3', 22.97, 'P3');

CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT, password TEXT, age INTEGER, favorite_book TEXT);
INSERT INTO users VALUES(NULL, 'vcherubini', 'password1', 25, 'xUnit Test Patterns');
INSERT INTO users VALUES(NULL, 'bsaget', 'password2', 50, 'The Olsen Twins: A Legacy');
INSERT INTO users VALUES(NULL, 'ggottfried', 'password3', 52, 'Its The Whiskey Talkin');
INSERT INTO users VALUES(NULL, 'howard_stern', 'password4', 56, 'Private Parts');