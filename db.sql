CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(36) NOT NULL UNIQUE,
    token VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    email_verified BOOLEAN DEFAULT FALSE,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    is_admin BOOLEAN DEFAULT FALSE,
    payloads JSON default NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS monieflow_coin_values (
    id INT AUTO_INCREMENT PRIMARY KEY,
    country_code VARCHAR(10) NOT NULL UNIQUE,
    country VARCHAR(100) NOT NULL UNIQUE,
    currency_code VARCHAR(10) NOT NULL UNIQUE,
    amount DECIMAL(36, 15) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS monieflow_coin_chart_table (
    id INT AUTO_INCREMENT PRIMARY KEY,
    country_code_id INT NOT NULL,
    currency_code VARCHAR(10) NOT NULL,
    amount DECIMAL(36, 15) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_country_code_id FOREIGN KEY (country_code_id) REFERENCES monieflow_coin_values(id) ON DELETE CASCADE,
    CONSTRAINT fk_currency_code FOREIGN KEY (currency_code) REFERENCES monieflow_coin_values(currency_code) ON DELETE CASCADE
);

INSERT INTO monieflow_coin_values (country_code, country, currency_code, amount) VALUES
('NG', 'Nigeria', 'NGN', 1.000000000000000),
('US', 'United States', 'USD', 1610.500000000000000),
('GB', 'United Kingdom', 'GBP', 2050.250000000000000),
('EU', 'Eurozone', 'EUR', 1745.800000000000000),
('CA', 'Canada', 'CAD', 1175.300000000000000),
('AU', 'Australia', 'AUD', 1060.400000000000000),
('GH', 'Ghana', 'GHS', 102.150000000000000),
('ZA', 'South Africa', 'ZAR', 88.600000000000000),
('KE', 'Kenya', 'KES', 12.450000000000000),
('AE', 'United Arab Emirates', 'AED', 438.500000000000000),
('CN', 'China', 'CNY', 224.300000000000000),
('JP', 'Japan', 'JPY', 10.850000000000000),
('IN', 'India', 'INR', 19.250000000000000),
('CH', 'Switzerland', 'CHF', 1830.100000000000000),
('BR', 'Brazil', 'BRL', 288.400000000000000),
('EG', 'Egypt', 'EGP', 33.200000000000000),
('RW', 'Rwanda', 'RWF', 1.200000000000000),
('UG', 'Uganda', 'UGX', 0.435000000000000),
('TZ', 'Tanzania', 'TZS', 0.605000000000000),
('CM', 'Cameroon', 'XAF', 2.660000000000000)
ON DUPLICATE KEY UPDATE 
    amount = VALUES(amount),
    updated_at = CURRENT_TIMESTAMP;


CREATE TABLE IF NOT EXISTS wallets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(36) NOT NULL UNIQUE,
    public_id VARCHAR(255) NOT NULL UNIQUE,
    private_key VARCHAR(255) NOT NULL,
    account_number VARCHAR(20) NOT NULL UNIQUE,
    balance DECIMAL(36, 15) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    constraint fk_user FOREIGN KEY (uid) REFERENCES users(uid) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS deposits (
    id int AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(36) NOT NULL,
    pin_code VARCHAR(10) NOT NULL,
    amount DECIMAL(36, 15) default 0.00,
    status ENUM('pending', 'completed', 'failed') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    constraint fk_deposit_user FOREIGN KEY (uid) REFERENCES users(uid) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS transaction (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(36) NOT NULL,
    amount DECIMAL(36, 15) NOT NULL,
    type ENUM('credit', 'debit') NOT NULL,
    description TEXT,
    payloads JSON default NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    constraint fk_transaction_user FOREIGN KEY (uid) REFERENCES users(uid) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS p2p_listings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(36) NOT NULL,
    type ENUM('buy', 'sell') NOT NULL,
    rate DECIMAL(36, 15) NOT NULL,
    amount DECIMAL(36, 15) NOT NULL,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_p2p_user FOREIGN KEY (uid) REFERENCES users(uid) ON DELETE CASCADE
);

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Chat Messages Table
CREATE TABLE IF NOT EXISTS p2p_chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    listing_id INT NOT NULL,
    sender_uid VARCHAR(36) NOT NULL,
    receiver_uid VARCHAR(36) NOT NULL,
    message TEXT DEFAULT NULL,
    type ENUM('text', 'escrow_init', 'escrow_released', 'system') DEFAULT 'text',
    payloads JSON DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_chat_listing FOREIGN KEY (listing_id) REFERENCES p2p_listings(id) ON DELETE CASCADE,
    CONSTRAINT fk_chat_sender FOREIGN KEY (sender_uid) REFERENCES users(uid) ON DELETE CASCADE,
    CONSTRAINT fk_chat_receiver FOREIGN KEY (receiver_uid) REFERENCES users(uid) ON DELETE CASCADE
);

-- 2. Escrow Transactions Tracking Table
CREATE TABLE IF NOT EXISTS p2p_escrows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    listing_id INT NOT NULL,
    buyer_uid VARCHAR(36) NOT NULL,
    seller_uid VARCHAR(36) NOT NULL,
    amount DECIMAL(36, 15) NOT NULL,
    deposit_pin VARCHAR(10) DEFAULT NULL,
    status ENUM('locked', 'released', 'cancelled') DEFAULT 'locked',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_escrow_listing FOREIGN KEY (listing_id) REFERENCES p2p_listings(id) ON DELETE CASCADE,
    CONSTRAINT fk_escrow_buyer FOREIGN KEY (buyer_uid) REFERENCES users(uid) ON DELETE CASCADE,
    CONSTRAINT fk_escrow_seller FOREIGN KEY (seller_uid) REFERENCES users(uid) ON DELETE CASCADE
);

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE IF NOT EXISTS `referrals` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `referrer_uid` VARCHAR(64) NOT NULL,
  `referred_uid` VARCHAR(64) NOT NULL,
  `reward_mf` DECIMAL(18, 4) DEFAULT 10.0000,
  `status` ENUM('pending', 'completed') DEFAULT 'completed',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (`referrer_uid`),
  INDEX (`referred_uid`)
);

-- 1. Tasks Table (Client creates tasks with fiat reward converted to MF)
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `client_uid` VARCHAR(64) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `proof_required` TEXT NOT NULL,
  `fiat_reward` DECIMAL(18, 2) NOT NULL,
  `currency_code` VARCHAR(10) DEFAULT 'NGN',
  `reward_mf` DECIMAL(18, 4) NOT NULL,
  `max_submissions` INT DEFAULT 100,
  `status` ENUM('active', 'paused', 'completed') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (`client_uid`),
  INDEX (`status`)
);

-- 2. Task Submissions Table (Members submit proof & get auto-paid or disputed)
CREATE TABLE IF NOT EXISTS `task_submissions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `task_id` INT NOT NULL,
  `member_uid` VARCHAR(64) NOT NULL,
  `proof_text` TEXT NOT NULL,
  `status` ENUM('pending', 'approved', 'disputed', 'rejected') DEFAULT 'pending',
  `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`task_id`) REFERENCES `tasks`(`id`) ON DELETE CASCADE,
  INDEX (`member_uid`),
  INDEX (`status`)
);

-- 3. Disputes Table (Client/Member dispute settlement system)
CREATE TABLE IF NOT EXISTS `task_disputes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `submission_id` INT NOT NULL,
  `opened_by_uid` VARCHAR(64) NOT NULL,
  `reason` TEXT NOT NULL,
  `admin_resolution` ENUM('pending', 'approved_member', 'rejected_member') DEFAULT 'pending',
  `resolved_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`submission_id`) REFERENCES `task_submissions`(`id`) ON DELETE CASCADE
);

-- 1. Artifact Items Table (Individual pieces uploaded by users)
CREATE TABLE IF NOT EXISTS `artifact_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `uid` VARCHAR(36) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `image_url` VARCHAR(255) DEFAULT NULL,
  `base_price` DECIMAL(36, 15) DEFAULT 0.00,
  `status` ENUM('available', 'linked', 'sold') DEFAULT 'available',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_items_user` FOREIGN KEY (`uid`) REFERENCES `users`(`uid`) ON DELETE CASCADE
);
ALTER TABLE `artifact_items` 
ADD COLUMN if not EXISTS `category` VARCHAR(100) DEFAULT 'Antique Relic' AFTER `description`;

-- 2. Artifacts Table (Formed when two items are linked together)
CREATE TABLE IF NOT EXISTS `artifacts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `item_one_id` INT NOT NULL,
  `item_two_id` INT NOT NULL,
  `artifact_name` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `estimated_value` DECIMAL(36, 15) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_artifact_item1` FOREIGN KEY (`item_one_id`) REFERENCES `artifact_items`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_artifact_item2` FOREIGN KEY (`item_two_id`) REFERENCES `artifact_items`(`id`) ON DELETE CASCADE
);


-- 3. Bidding Listings Table
CREATE TABLE IF NOT EXISTS `bounty_auctions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `seller_uid` VARCHAR(36) NOT NULL,
  `artifact_id` INT DEFAULT NULL, -- Null if bidding on a standalone item
  `item_id` INT DEFAULT NULL,     -- Null if bidding on a full artifact
  `starting_bid` DECIMAL(36, 15) NOT NULL,
  `current_bid` DECIMAL(36, 15) NOT NULL,
  `status` ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
  `expires_at` TIMESTAMP NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_auction_seller` FOREIGN KEY (`seller_uid`) REFERENCES `users`(`uid`) ON DELETE CASCADE,
  CONSTRAINT `fk_auction_artifact` FOREIGN KEY (`artifact_id`) REFERENCES `artifacts`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_auction_item` FOREIGN KEY (`item_id`) REFERENCES `artifact_items`(`id`) ON DELETE CASCADE
);

-- 4. Bids History Table
CREATE TABLE IF NOT EXISTS `auction_bids` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `auction_id` INT NOT NULL,
  `bidder_uid` VARCHAR(36) NOT NULL,
  `bid_amount` DECIMAL(36, 15) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_bid_auction` FOREIGN KEY (`auction_id`) REFERENCES `bounty_auctions`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_bid_user` FOREIGN KEY (`bidder_uid`) REFERENCES `users`(`uid`) ON DELETE CASCADE
);

-- Join table for linking two distinct artifact items together into a combined artifact
CREATE TABLE IF NOT EXISTS `item_links` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `initiator_uid` VARCHAR(36) NOT NULL,
  `item_one_id` INT NOT NULL,
  `item_two_id` INT NOT NULL,
  `status` ENUM('pending', 'linked', 'rejected') DEFAULT 'linked',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_link_user` FOREIGN KEY (`initiator_uid`) REFERENCES `users`(`uid`) ON DELETE CASCADE,
  CONSTRAINT `fk_link_item1` FOREIGN KEY (`item_one_id`) REFERENCES `artifact_items`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_link_item2` FOREIGN KEY (`item_two_id`) REFERENCES `artifact_items`(`id`) ON DELETE CASCADE
);





-- 1. Insert sample user if you don't already have one
INSERT INTO users (uid, token, full_name, username, email, phone, password)
VALUES ('user-seller-001', 'token123', 'Seller User', 'seller', 'seller@example.com', '1234567890', 'hashed_pass')
ON DUPLICATE KEY UPDATE uid=uid;

-- 2. Insert Artifact Items into `artifact_items`
INSERT INTO artifact_items (id, uid, title, description, category, base_price, status) VALUES
(1, 'user-seller-001', 'Golden Crown Left Fragment', 'Left side of an ancient royal crown crafted in pure gold.', 'Royal Relic', 150.00, 'available'),
(2, 'user-seller-001', 'Golden Crown Right Fragment', 'Right side of an ancient royal crown crafted in pure gold.', 'Royal Relic', 150.00, 'available'),
(3, 'user-seller-001', 'Ancient Dragon Hilt', 'Ornate dragon-head sword hilt encrusted with rubies.', 'Weaponry', 220.00, 'available'),
(4, 'user-seller-001', 'Obsidian Blade Segment', 'A blade fragment forged from dark volcanic glass.', 'Weaponry', 180.00, 'available'),
(5, 'user-seller-001', 'Mystic Sun Dial Lens', 'Glass optic component used in solar astronomical devices.', 'Astrology', 95.00, 'available'),
(6, 'user-seller-001', 'Bronze Sun Dial Stand', 'Heavy engraved bronze base for celestial measurement.', 'Astrology', 110.00, 'available'),
(7, 'user-seller-001', 'Jade Dragon Gem Eye', 'Deep green carved jade gem fitting into ancient statues.', 'Antique Relic', 310.00, 'available'),
(8, 'user-seller-001', 'Ancient Tome Cover', 'Leather bound book spine with silver runes.', 'Manuscript', 75.00, 'available'),
(9, 'user-seller-001', 'Enchanted Tome Pages', 'Illuminated parchment leaves with elemental inscriptions.', 'Manuscript', 125.00, 'available')
ON DUPLICATE KEY UPDATE status='available';

-- 3. Link pairs into combined artifacts in `artifacts`
INSERT INTO artifacts (id, item_one_id, item_two_id, artifact_name, description, estimated_value) VALUES
(1, 1, 2, 'The Sovereign Crown of Kings', 'Fully restored royal crown granting immense prestige.', 500.00),
(2, 3, 4, 'Obsidian Dragon Greatsword', 'Formidable sword assembled from dragon hilt and obsidian blade.', 650.00),
(3, 5, 6, 'Celestial Sun Compass', 'Ancient astronomical tracker capable of solar alignment.', 300.00)
ON DUPLICATE KEY UPDATE artifact_name=VALUES(artifact_name);