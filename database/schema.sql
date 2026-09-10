-- Strike Elite database schema.
-- Import via phpMyAdmin or: mysql -u root -p < schema.sql

CREATE DATABASE IF NOT EXISTS strike_elite CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE strike_elite;

-- users
CREATE TABLE IF NOT EXISTS users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    email         VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- categories
CREATE TABLE IF NOT EXISTS categories (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT INTO categories (name, slug) VALUES
    ('Soccer Boots', 'soccer-boots'),
    ('Jersey',       'jersey'),
    ('Equipment',    'equipment')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- products
CREATE TABLE IF NOT EXISTS products (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    category_id   INT,
    name          VARCHAR(150) NOT NULL,
    slug          VARCHAR(150) NOT NULL UNIQUE,
    description   TEXT,
    price         DECIMAL(10,2) NOT NULL,
    stock         INT NOT NULL DEFAULT 0,
    image         VARCHAR(255) DEFAULT 'placeholder.png',
    rating        DECIMAL(2,1) DEFAULT 5.0,
    is_bestseller TINYINT(1) DEFAULT 0,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB;

INSERT INTO products (category_id, name, slug, description, price, stock, image, rating, is_bestseller) VALUES
((SELECT id FROM categories WHERE slug='soccer-boots'), 'Strike Elite Phantom Boots', 'phantom-boots',
 'Precision-engineered soccer boots built for speed, control, and peak performance on the pitch.', 5499.00, 14, 'phantom-boots.png', 5.0, 1),
((SELECT id FROM categories WHERE slug='soccer-boots'), 'Strike Elite Vortex Pro FG', 'vortex-pro-fg',
 'Firm-ground boots with an aggressive stud pattern for explosive acceleration and sharp cuts.', 1999.00, 20, 'vortex-pro-fg.png', 5.0, 1),
((SELECT id FROM categories WHERE slug='jersey'), 'Strike Elite Titan Jersey', 'titan-jersey',
 'Lightweight, breathable match jersey with moisture-wicking fabric for 90 minutes of comfort.', 1199.00, 30, 'titan-jersey.png', 5.0, 1),
((SELECT id FROM categories WHERE slug='equipment'), 'Strike Elite Match Ball', 'match-ball',
 'FIFA-quality match ball with consistent flight and premium durability.', 1500.00, 25, 'match-ball.png', 5.0, 1),
((SELECT id FROM categories WHERE slug='equipment'), 'Strike Elite Goalkeeper Gloves', 'goalkeeper-gloves',
 'Latex palm goalkeeper gloves engineered for maximum grip in all weather conditions.', 1350.00, 18, 'goalkeeper-gloves.png', 4.8, 0)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- orders
CREATE TABLE IF NOT EXISTS orders (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    user_id           INT NOT NULL,
    total_amount      DECIMAL(10,2) NOT NULL,
    payment_method    VARCHAR(30) NOT NULL,
    status            VARCHAR(30) DEFAULT 'pending',
    shipping_name     VARCHAR(150),
    shipping_address  VARCHAR(255),
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- order_items (line items for each order)
CREATE TABLE IF NOT EXISTS order_items (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    order_id   INT NOT NULL,
    product_id INT NOT NULL,
    quantity   INT NOT NULL,
    price      DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

-- messages submitted through the Contact Us page
CREATE TABLE IF NOT EXISTS contact_messages (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(100) NOT NULL,
    message    TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- emails collected from the newsletter signup in the footer
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    email      VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
