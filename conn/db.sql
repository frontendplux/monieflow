CREATE TABLE IF NOT EXISTS users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(305) NOT NULL,
    email VARCHAR(300) NOT NULL,
    password VARCHAR(305) NOT NULL,
    profile JSON DEFAULT '{}',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS feeds (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    data JSON DEFAULT '{}',
    status VARCHAR(100) NOT NULL, -- feed, reel, market, groups, pages, ...
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS likes (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    feed_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    parent_id INT NOT NULL,
    data JSON DEFAULT '{}',
    status VARCHAR(100) DEFAULT 'feed', -- feed, reel, groups, pages, ...
    type VARCHAR(100) DEFAULT 'post',   -- comment, post
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS events_trigger (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    event_type VARCHAR(100) DEFAULT 'like', -- 'like','comment','share','follow','message','notification',...
    data JSON DEFAULT '{}',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

DELETE FROM events_trigger 
WHERE created_at < (NOW() - INTERVAL 25 MINUTE);
