-- ISDN Complete Database
DROP DATABASE IF EXISTS isdn;
CREATE DATABASE isdn;
USE isdn;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('Admin','RDC','Head Office','Retail Customer','Driver','Logistics Officer') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  retailer VARCHAR(255) NOT NULL,
  category VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  image VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  customer_name VARCHAR(120) NOT NULL,
  address TEXT NOT NULL,
  payment_method VARCHAR(60) NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  status ENUM('Pending','Processing','Completed','Cancelled') DEFAULT 'Pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE deliveries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  driver_name VARCHAR(100) NOT NULL,
  route_details VARCHAR(255) NOT NULL,
  delivery_status ENUM('Pending','In Transit','Delivered') DEFAULT 'Pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_deliveries_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

CREATE TABLE stock_transfers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_name VARCHAR(100) NOT NULL,
  from_rdc VARCHAR(100) NOT NULL,
  to_rdc VARCHAR(100) NOT NULL,
  quantity INT NOT NULL,
  status VARCHAR(50) NOT NULL DEFAULT 'Pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE inventory (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  rdc_name VARCHAR(100) NOT NULL DEFAULT 'Central RDC',
  stock_count INT NOT NULL DEFAULT 0,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_inventory_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  UNIQUE KEY uq_inventory_product_rdc (product_id, rdc_name)
);

INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@gmail.com', '$2y$12$p1luKyMIv9bl43fDx/Jppeunje81bYYR3/Uq47POOhOe/4leu4k8y', 'Admin'),
('RDC Officer', 'rdc@gmail.com', '$2y$12$p1luKyMIv9bl43fDx/Jppeunje81bYYR3/Uq47POOhOe/4leu4k8y', 'RDC'),
('Retail Customer', 'customer@gmail.com', '$2y$12$p1luKyMIv9bl43fDx/Jppeunje81bYYR3/Uq47POOhOe/4leu4k8y', 'Retail Customer'),
('Head Office Manager', 'homanager@gmail.com', '$2y$12$p1luKyMIv9bl43fDx/Jppeunje81bYYR3/Uq47POOhOe/4leu4k8y', 'Head Office'),
('Logistics Officer', 'logisticsofficer@gmail.com', '$2y$12$p1luKyMIv9bl43fDx/Jppeunje81bYYR3/Uq47POOhOe/4leu4k8y', 'Logistics Officer'),
('Driver', 'driver@gmail.com', '$2y$12$p1luKyMIv9bl43fDx/Jppeunje81bYYR3/Uq47POOhOe/4leu4k8y', 'Driver');

INSERT INTO products (name, price, retailer, category, description, image) VALUES
('Rice', 2500.00, 'ABC Supermarket', 'Packaged Food', 'Premium rice suitable for retail distribution.', 'rice.jpg'),
('Flour', 450.00, 'City Retailers', 'Packaged Food', 'Multi-purpose wheat flour for wholesale orders.', 'flour.jpg'),
('Canned Food', 700.00, 'Metro Traders', 'Packaged Food', 'Ready-to-sell canned food stock for resellers.', 'cannedfood.jpg'),
('Tomatoes', 300.00, 'Fresh Farm Hub', 'Vegetables', 'Fresh tomatoes for supermarkets and shops.', 'tomatoes.jpg'),
('Soap', 220.00, 'Island Retail', 'Household Items', 'Personal care product with strong retailer demand.', 'soap.jpg'),
('Oranges', 400.00, 'ABC Supermarket', 'Fruits', 'Fresh oranges rich in vitamin C.', 'best-product-1.jpg');

INSERT INTO inventory (product_id, rdc_name, stock_count) VALUES
(1, 'Central RDC', 120),
(2, 'Central RDC', 90),
(3, 'Central RDC', 75),
(4, 'Central RDC', 150),
(5, 'Central RDC', 200),
(6, 'Central RDC', 110);

INSERT INTO orders (user_id, customer_name, address, payment_method, total_amount, status) VALUES
(3, 'Retail Customer', 'No. 12, Negombo Road, Colombo', 'Cash on Delivery', 2950.00, 'Pending'),
(3, 'Retail Customer', 'No. 12, Negombo Road, Colombo', 'Online Payment', 920.00, 'Completed');

INSERT INTO order_items (order_id, product_id, quantity, price) VALUES
(1, 1, 1, 2500.00),
(1, 4, 1, 300.00),
(1, 5, 1, 150.00),
(2, 2, 1, 450.00),
(2, 6, 1, 400.00),
(2, 5, 1, 70.00);

INSERT INTO deliveries (order_id, driver_name, route_details, delivery_status) VALUES
(1, 'Saman Perera', 'Central RDC -> Colombo 10', 'Pending'),
(2, 'Nuwan Silva', 'Western RDC -> Negombo', 'Delivered');

INSERT INTO stock_transfers (product_name, from_rdc, to_rdc, quantity, status) VALUES
('Rice', 'Central RDC', 'Northern RDC', 50, 'Approved'),
('Soap', 'Western RDC', 'Southern RDC', 80, 'Pending'),
('Canned Food', 'Eastern RDC', 'Central RDC', 40, 'Completed');
