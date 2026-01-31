
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