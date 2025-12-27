CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uids VARCHAR(300) NOT NULL,
    email VARCHAR(300) NOT NULL UNIQUE,
    user VARCHAR(100) NOT NULL,
    password_hash VARCHAR(300) NOT NULL,
    profile JSON DEFAULT '{}',
    whatsapp VARCHAR(50) NOT NULL,
    wallet JSON DEFAULT '{}',
    roles ENUM('member','admin') DEFAULT 'member',
    status ENUM('pending','active','warning','blocked') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- If you're on MySQL 8.0.21+, you can use IF NOT EXISTS
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS first_name VARCHAR(100) AFTER uids,
  ADD COLUMN IF NOT EXISTS last_name VARCHAR(100) AFTER first_name,
  ADD COLUMN IF NOT EXISTS phone VARCHAR(100) AFTER last_name,
  ADD COLUMN IF NOT EXISTS referral VARCHAR(100) AFTER profile;

CREATE TABLE IF NOT EXISTS currency ( 
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
    icons VARCHAR(300) NOT NULL, 
    name VARCHAR(200) NOT NULL UNIQUE, 
    amount DECIMAL(10,2) DEFAULT 0.00, 
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP 
    ) ENGINE=InnoDB;
 
CREATE TABLE IF NOT EXISTS wallets ( 
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
        wallet_balance DECIMAL(10,2) DEFAULT 0.00, 
        cvc CHAR(4) NOT NULL, 
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
        user_id INT UNSIGNED NOT NULL, 
        currency_id INT UNSIGNED NOT NULL, 
        CONSTRAINT fk_wallet_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        CONSTRAINT fk_wallet_currency FOREIGN KEY (currency_id) REFERENCES currency(id) ON DELETE CASCADE 
        ) AUTO_INCREMENT=1000000000 ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sender_wallet_id BIGINT UNSIGNED NOT NULL,
    recipient_wallet_id BIGINT UNSIGNED NOT NULL,
    sender_user_id INT UNSIGNED NOT NULL,
    recipient_user_id INT UNSIGNED NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency_id INT UNSIGNED NOT NULL,
    status ENUM('pending','completed','failed') DEFAULT 'pending',
    statusView ENUM('seen','unseen') DEFAULT 'unseen',
    message VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_tx_sender_wallet FOREIGN KEY (sender_wallet_id) REFERENCES wallets(id) ON DELETE CASCADE,
    CONSTRAINT fk_tx_recipient_wallet FOREIGN KEY (recipient_wallet_id) REFERENCES wallets(id) ON DELETE CASCADE,
    CONSTRAINT fk_tx_sender_user FOREIGN KEY (sender_user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_tx_recipient_user FOREIGN KEY (recipient_user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_tx_currency FOREIGN KEY (currency_id) REFERENCES currency(id) ON DELETE CASCADE
) ENGINE=InnoDB;
ALTER TABLE transactions
ADD COLUMN IF NOT EXISTS transaction_type ENUM('transfer','withdraw','deposit','payment','system','alert') DEFAULT 'transfer' AFTER amount;


CREATE TABLE IF NOT EXISTS notifications (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  transaction_id BIGINT UNSIGNED DEFAULT NULL,
  transaction_type ENUM('transfer','payment','system','alert') DEFAULT 'system',
  title VARCHAR(200) NOT NULL,
  message TEXT NOT NULL,
  status ENUM('unread','read') DEFAULT 'unread',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  CONSTRAINT fk_notification_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_notification_transaction FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE SET NULL
) ENGINE=InnoDB;



CREATE TABLE IF NOT EXISTS giftcard_transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    card_code VARCHAR(100) NOT NULL UNIQUE,
    card_type VARCHAR(100) NOT NULL,
    currency_id INT UNSIGNED NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    transaction_type ENUM('purchase','redemption') NOT NULL,
    status ENUM('pending','completed','failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_giftcard_tx_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_giftcard_tx_currency FOREIGN KEY (currency_id) REFERENCES currency(id) ON DELETE CASCADE
) ENGINE=InnoDB;


DROP TRIGGER IF EXISTS before_insert_giftcard;

CREATE TRIGGER before_insert_giftcard
BEFORE INSERT ON giftcard_transactions
FOR EACH ROW
BEGIN
  IF NEW.card_code IS NULL OR NEW.card_code = '' THEN
    SET NEW.card_code = CONCAT(
      'GC-',
      UPPER(SUBSTRING(MD5(RAND()),1,4)), '-',
      UPPER(SUBSTRING(MD5(RAND()),5,4)), '-',
      UPPER(SUBSTRING(MD5(RAND()),9,4))
    );
  END IF;
END;
