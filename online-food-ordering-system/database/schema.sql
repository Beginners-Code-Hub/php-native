-- FoodDash schema. MySQL 5.7+/8 and MariaDB 10.4+.
CREATE DATABASE IF NOT EXISTS fooddash CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fooddash;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS rider_locations, order_status_log, order_items, orders, products, categories, users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  phone         VARCHAR(30)  NULL,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('customer','admin','rider') NOT NULL DEFAULT 'customer',
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
  id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name  VARCHAR(80) NOT NULL UNIQUE,
  icon  VARCHAR(40) NOT NULL DEFAULT 'restaurant'
) ENGINE=InnoDB;

CREATE TABLE products (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id  INT UNSIGNED NOT NULL,
  name         VARCHAR(120) NOT NULL,
  description  VARCHAR(255) NOT NULL DEFAULT '',
  price        DECIMAL(10,2) NOT NULL,
  emoji        VARCHAR(16)  NOT NULL DEFAULT '🍽️',
  image        VARCHAR(255) NULL,
  is_available TINYINT(1) NOT NULL DEFAULT 1,
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB;

CREATE TABLE orders (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id      INT UNSIGNED NOT NULL,
  rider_id     INT UNSIGNED NULL,
  status       ENUM('pending','preparing','out_for_delivery','delivered','cancelled') NOT NULL DEFAULT 'pending',
  subtotal     DECIMAL(10,2) NOT NULL,
  delivery_fee DECIMAL(10,2) NOT NULL DEFAULT 0,
  total        DECIMAL(10,2) NOT NULL,
  area         VARCHAR(60)  NOT NULL,
  address      VARCHAR(255) NOT NULL,
  phone        VARCHAR(30)  NOT NULL,
  notes        VARCHAR(255) NULL,
  dest_x       TINYINT UNSIGNED NOT NULL,
  dest_y       TINYINT UNSIGNED NOT NULL,
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_user  FOREIGN KEY (user_id)  REFERENCES users(id),
  CONSTRAINT fk_orders_rider FOREIGN KEY (rider_id) REFERENCES users(id),
  INDEX idx_orders_status (status)
) ENGINE=InnoDB;

CREATE TABLE order_items (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id   INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  name       VARCHAR(120) NOT NULL,
  qty        SMALLINT UNSIGNED NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_items_order   FOREIGN KEY (order_id)   REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_items_product FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

CREATE TABLE order_status_log (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id   INT UNSIGNED NOT NULL,
  status     VARCHAR(30) NOT NULL,
  changed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_log_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE rider_locations (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id    INT UNSIGNED NOT NULL,
  x           DECIMAL(5,2) NOT NULL,
  y           DECIMAL(5,2) NOT NULL,
  recorded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_loc_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  INDEX idx_loc_order (order_id, id)
) ENGINE=InnoDB;
