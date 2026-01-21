/* DROP TABLE IF EXISTS users; */
/* CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    uids VARCHAR(300) NOT NULL,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(300),
    password_hash VARCHAR(255) NOT NULL,

    profile JSON NOT NULL DEFAULT (JSON_OBJECT()),
    status enum('pending','active','warning') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
     
    UNIQUE KEY uq_email (email),
    UNIQUE KEY uq_uids (uids)
);
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS phone VARCHAR(30) AFTER email;
ALTER TABLE users ADD UNIQUE KEY unique_email (email);
ALTER TABLE users ADD UNIQUE KEY unique_phone (phone);
ALTER TABLE users ADD UNIQUE KEY unique_uids (uids); */


CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uids VARCHAR(300) NOT NULL,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(300),
    phone VARCHAR(30), -- Added phone here directly
    password_hash VARCHAR(255) NOT NULL,

    profile JSON NOT NULL DEFAULT (JSON_OBJECT()),
    status enum('pending','active','warning') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
     
    -- Define all Unique Keys here once
    UNIQUE KEY uq_email (email),
    UNIQUE KEY uq_uids (uids),
    UNIQUE KEY uq_phone (phone)
);



/* DROP TABLE IF EXISTS feeds; */
CREATE TABLE IF NOT EXISTS feeds (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,        -- who created the feed
    content TEXT NOT NULL,                   -- main text/post content
    media JSON DEFAULT '{}',       -- optional images/videos/attachments
    type ENUM('post','update','product','event') DEFAULT 'post', -- feed type
    shares INT UNSIGNED DEFAULT 0,           -- number of shares
    status ENUM('active','hidden','deleted') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);



-- Comments table
/* DROP TABLE IF EXISTS comments; */
CREATE TABLE IF NOT EXISTS comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    feed_id BIGINT UNSIGNED NOT NULL,       -- which feed the comment belongs to
    user_id BIGINT UNSIGNED NOT NULL,       -- who made the comment
    reply_id BIGINT UNSIGNED DEFAULT NULL,  -- optional: reply to another comment
    content TEXT NOT NULL,       
    status ENUM('active','hidden','deleted') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (feed_id) REFERENCES feeds(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reply_id) REFERENCES comments(id) ON DELETE CASCADE
);

-- Likes table
/* DROP TABLE IF EXISTS likes; */
CREATE TABLE IF NOT EXISTS likes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    feed_id BIGINT UNSIGNED DEFAULT NULL,      -- feed liked (optional if liking a comment)
    comment_id BIGINT UNSIGNED DEFAULT NULL,   -- comment liked (optional if liking a feed)
    user_id BIGINT UNSIGNED NOT NULL,          -- who liked it

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- prevent duplicate likes on the same target by the same user
    UNIQUE KEY uq_like (feed_id, comment_id, user_id),

    FOREIGN KEY (feed_id) REFERENCES feeds(id) ON DELETE CASCADE,
    FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);




-- Categories table
/* DROP TABLE IF EXISTS categories; */
CREATE TABLE IF NOT EXISTS categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL UNIQUE,       -- category name
    keyword VARCHAR(255) DEFAULT NULL,       -- SEO keyword or tag
    img VARCHAR(255) DEFAULT NULL,           -- category image (path or URL)
    description TEXT DEFAULT NULL,           -- optional description
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Products table
/* DROP TABLE IF EXISTS products; */
CREATE TABLE IF NOT EXISTS products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED NOT NULL,    -- link to category
    user_id BIGINT UNSIGNED NOT NULL,        -- owner/creator of product
    name VARCHAR(200) NOT NULL,              -- product name
    description TEXT DEFAULT NULL,           -- product description
    price DECIMAL(10,2) NOT NULL,            -- product price
    off_price DECIMAL(10,2) DEFAULT NULL,    -- discounted price (optional)
    stock INT UNSIGNED DEFAULT 0,            -- available quantity
    media JSON DEFAULT (JSON_ARRAY()),       -- product images
    varient JSON DEFAULT '{}',               -- product variants (size, color, etc.)
    status ENUM('active','inactive','deleted') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS friends(
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    follower INT NOT NULL,
    following INT NOT null,
    status enum('seen','unseen')  DEFAULT 'unseen',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


/* DROP TABLE IF EXISTS products; */
CREATE TABLE IF NOT EXISTS products(
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id BIGINT UNSIGNED NOT NULL,        -- product owner / seller
    category_id BIGINT UNSIGNED DEFAULT NULL, -- linked category (optional)

    name VARCHAR(200) NOT NULL,              -- product name
    description TEXT DEFAULT NULL,           -- full product description

    location VARCHAR(255) NOT NULL,          -- product location (city / shop / market)

    price DECIMAL(10,2) NOT NULL,            -- selling price
    market_price DECIMAL(10,2) DEFAULT NULL, -- main/market price (optional)

    stock INT UNSIGNED DEFAULT 0,             -- available quantity

    variants JSON DEFAULT (JSON_OBJECT()),   -- size, color, model, etc
    media JSON DEFAULT (JSON_ARRAY()),        -- images/videos paths

    status ENUM('active','inactive','sold','deleted') DEFAULT 'active',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
        ON UPDATE CURRENT_TIMESTAMP,

    -- indexes & relations
    KEY idx_user (user_id),
    KEY idx_category (category_id),
    KEY idx_location (location),

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);
