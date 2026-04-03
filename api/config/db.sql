-- =========================
-- TABLES
-- =========================

CREATE TABLE IF NOT EXISTS current_update (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(100) NOT NULL,
    post_id BIGINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_current_update_post_id (post_id),
    INDEX idx_current_update_created_at (created_at)
);

CREATE TABLE IF NOT EXISTS stages (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    keyword VARCHAR(300) NOT NULL,
    description VARCHAR(300) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(300) NOT NULL,
    username VARCHAR(100) NOT NULL,
    bio VARCHAR(300) NOT NULL,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    email VARCHAR(300) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(300) NOT NULL,
    stage INT NOT NULL,
    avatar VARCHAR(300) NULL,
    reset_password_token VARCHAR(300) NULL,
    reset_password_expires TIMESTAMP NULL,
    is_reset BOOLEAN,
    sex VARCHAR(20) NOT NULL,
    dob DATE NOT NULL,
    others JSON DEFAULT '{}',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_users_uid (uid),
    INDEX idx_users_username (username),
    INDEX idx_users_email (email),
    INDEX idx_users_stage (stage)
);

alter table  users 
add column if not exists country VARCHAR(100) after password;

CREATE TABLE IF NOT EXISTS gallery (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    image VARCHAR(300) NULL,
    video VARCHAR(300) NULL,
    audio VARCHAR(300) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_gallery_user_id (user_id)
);

CREATE TABLE IF NOT EXISTS feeds (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    parent_id BIGINT NULL,
    content TEXT NOT NULL,
    image JSON DEFAULT '{}',
    video VARCHAR(300) NULL,
    audio VARCHAR(300) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_feeds_user_id (user_id),
    INDEX idx_feeds_parent_id (parent_id),
    INDEX idx_feeds_created_at (created_at),
    INDEX idx_feeds_user_created (user_id, created_at),
    FULLTEXT INDEX ft_feeds_content (content)
);
ALTER TABLE feeds 
ADD COLUMN IF NOT EXISTS feed_type VARCHAR(100) DEFAULT 'feed';

CREATE TABLE if not EXISTS remove_post(
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(100) NOT NULL,
    user_id BIGINT NOT NULL,
    data_id BIGINT NOT NULL,
    reason VARCHAR(300) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_remove_post_data_id (data_id),
    INDEX idx_remove_post_user_id (user_id)
);

CREATE TABLE IF NOT EXISTS comments (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    feed_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    parent_id BIGINT NULL,
    content VARCHAR(300) NOT NULL,
    image VARCHAR(300) NULL,
    video VARCHAR(300) NULL,
    audio VARCHAR(300) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_comments_feed_id (feed_id),
    INDEX idx_comments_user_id (user_id),
    INDEX idx_comments_parent_id (parent_id),
    FULLTEXT INDEX ft_comments_content (content)
);

CREATE TABLE IF NOT EXISTS likes (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    like_type VARCHAR(100) NOT NULL,
    feed_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    comment_id BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_likes_feed_id (feed_id),
    INDEX idx_likes_user_id (user_id),
    INDEX idx_likes_comment_id (comment_id),
    UNIQUE INDEX idx_likes_unique (user_id, feed_id, comment_id)
);

CREATE TABLE IF NOT EXISTS share (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    feed_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    share_type VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_share_user_id (user_id),
    INDEX idx_share_feed_id (feed_id)
);

CREATE TABLE IF NOT EXISTS gifts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    image VARCHAR(300) NOT NULL,
    price DECIMAL(20,10) NOT NULL
);

CREATE TABLE IF NOT EXISTS tips (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    feed_id BIGINT NULL,
    comment_id BIGINT NULL,
    message_id BIGINT NULL,
    gift_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_tips_user_id (user_id),
    INDEX idx_tips_feed_id (feed_id),
    INDEX idx_tips_comment_id (comment_id)
);

CREATE TABLE IF NOT EXISTS followers (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    follower_id BIGINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_followers_user_id (user_id),
    INDEX idx_followers_follower_id (follower_id),
    INDEX idx_followers_user_follower (user_id, follower_id)
);

CREATE TABLE IF NOT EXISTS notifications (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    type VARCHAR(100) NOT NULL,
    icon VARCHAR(300) NULL,
    content_id BIGINT NULL,
    content TEXT NOT NULL,
    isread BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_notifications_user_id (user_id),
    INDEX idx_notifications_isread (isread)
);

CREATE TABLE IF NOT EXISTS messages (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    sender_id BIGINT NOT NULL,
    receiver_id BIGINT NOT NULL,
    video VARCHAR(300) NULL,
    image VARCHAR(300) NULL,
    audio VARCHAR(300) NULL,
    tips BIGINT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_messages_sender_id (sender_id),
    INDEX idx_messages_receiver_id (receiver_id),
    INDEX idx_messages_created_at (created_at),
    INDEX idx_messages_chat (sender_id, receiver_id, created_at)
);

CREATE TABLE IF NOT EXISTS reports (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    type VARCHAR(100) NOT NULL,
    content_id BIGINT NOT NULL,
    reason VARCHAR(300) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_reports_user_id (user_id),
    INDEX idx_reports_content_id (content_id)
);

CREATE TABLE IF NOT EXISTS currency (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    symbol VARCHAR(20) NOT NULL,
    code_number INT NOT NULL,
    code VARCHAR(100) NOT NULL,
    amount DECIMAL(20,10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_currency_code (code)
);

CREATE TABLE IF NOT EXISTS crypto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    symbol VARCHAR(20) NOT NULL,
    code_number INT NOT NULL,
    code VARCHAR(100) NOT NULL,
    amount DECIMAL(20,10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_crypto_code (code)
);

CREATE TABLE IF NOT EXISTS crypto_wallet_buy_and_sell (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    crypto_id INT NOT NULL,
    current_balance DECIMAL(20,10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS user_wallet (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    currency_id INT NULL,
    crypto_id INT NULL,
    balance DECIMAL(20,10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_user_wallet_user_id (user_id),
    INDEX idx_user_wallet_currency_id (currency_id),
    INDEX idx_user_wallet_crypto_id (crypto_id)
);

CREATE TABLE IF NOT EXISTS transactions (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    type VARCHAR(100) NOT NULL,
    amount DECIMAL(20,10) NOT NULL,
    currency_id INT NULL,
    crypto_id INT NULL,
    datas JSON DEFAULT '{}',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_transactions_user_id (user_id),
    INDEX idx_transactions_currency_id (currency_id),
    INDEX idx_transactions_crypto_id (crypto_id),
    INDEX idx_transactions_created_at (created_at)
);
