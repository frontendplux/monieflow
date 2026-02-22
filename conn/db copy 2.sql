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

-- Add category column if it doesn't exist
ALTER TABLE feeds
ADD COLUMN if not exists category TEXT NOT NULL AFTER user_id,
ADD COLUMN if not exists txt TEXT NOT NULL AFTER category,
ADD COLUMN if not exists description TEXT  NOT NULL AFTER txt,
ADD COLUMN if not exists keywords TEXT  NOT NULL AFTER description,
ADD COLUMN if not exists subscription TEXT  NOT NULL AFTER description;


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
ALTER TABLE likes
ADD COLUMN if not exists comment_id TEXT NOT NULL AFTER user_id;


CREATE TABLE IF NOT EXISTS comments (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    feed_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    parent_id INT NOT NULL,
    data JSON DEFAULT '{}',
    status VARCHAR(100) DEFAULT 'feed', -- feed, reel, groups, pages, ...
    type VARCHAR(100) DEFAULT 'post',   -- comment, post
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



CREATE TABLE IF NOT EXISTS flows (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    flow BIGINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



CREATE TABLE IF NOT EXISTS events_trigger (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    event_type VARCHAR(100) DEFAULT 'like', -- 'like','comment','share','follow','message','notification',...
    data JSON DEFAULT '{}',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
ALTER TABLE events_trigger
ADD COLUMN if NOT EXISTS feed_id BIGINT NOT NULL AFTER event_type;


DELETE FROM events_trigger 
WHERE created_at < (NOW() - INTERVAL 25 MINUTE);
