CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  email VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
  id SERIAL PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  price VARCHAR(100) NOT NULL,
  image VARCHAR(100) NOT NULL
);
INSERT INTO products ("id","name", "price", "image") VALUES
( '1','Album Born Pink 2022', '365.000', 'Album Born Pink 2022.jpeg'),
( '2','Foto Card', '40.000', 'Foto Card.jpg'),
( '3','Hoodie H&M x Blackpink', '599.000', 'Hoodie H&M x Blackpink.png'),
( '4','Blackpink Lightstick Ver.2', '780.000', 'Blackpink Lightstick Ver.2.jpg'),
( '5','Album The Album 2020', '339.000', 'Album The Album 2020.jpg'),
( '6','Tumbler Starbucks x Blackpink', '499.000', 'Tumbler Starbucks x Blackpink.jpeg');

CREATE TABLE cart (
  id SERIAL PRIMARY KEY,
  user_id INT REFERENCES users(id),
  name VARCHAR(100) NOT NULL,
  price VARCHAR(100) NOT NULL,
  image VARCHAR(100) NOT NULL,
  quantity INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
  id SERIAL PRIMARY KEY,
  user_id INT REFERENCES users(id),
	name VARCHAR(100) NOT NULL,
  number VARCHAR(15),
	email VARCHAR(50) NOT NULL,
  method VARCHAR(50) NOT NULL,
	flat VARCHAR(50),
  street VARCHAR(100),
	city VARCHAR(100),
	province VARCHAR(100),
	country VARCHAR(100),
	pin_code VARCHAR(20),
	total_product,
  price_total DECIMAL(10, 2),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
