CREATE TABLE IF NOT EXISTS users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(305) NOT NULL,
    email VARCHAR(300) NOT NULL,
    password VARCHAR(305) NOT NULL,
    profile JSON DEFAULT '{}',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS feeders (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    
    -- who created the post (can be user, page, group, event, market)
    source_id BIGINT NOT NULL,
    source_type ENUM('user','page','group','event','market') DEFAULT 'user',
    
    content TEXT,                            -- text content of the post
    media_url JSON DEFAULT (JSON_OBJECT()),  -- optional image/video/audio paths
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    likes_count INT DEFAULT 0,
    comments_count INT DEFAULT 0,
    shares_count INT DEFAULT 0,
    
    is_active TINYINT(1) DEFAULT 1
);


CREATE TABLE IF NOT EXISTS feed_likes (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    feed_id BIGINT NOT NULL,          -- the post being liked
    user_id BIGINT NOT NULL,          -- who liked the post
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_feedlikes_feed FOREIGN KEY (feed_id) REFERENCES feeders(id),
    CONSTRAINT fk_feedlikes_user FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY unique_like (feed_id, user_id) -- prevent duplicate likes
);


CREATE TABLE IF NOT EXISTS feed_comments (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    feed_id BIGINT NOT NULL,          -- the post being commented on
    user_id BIGINT NOT NULL,          -- who wrote the comment
    parent_id BIGINT DEFAULT NULL,    -- if this is a reply to another comment
    comment TEXT NOT NULL,            -- the actual comment text
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_feedcomments_feed FOREIGN KEY (feed_id) REFERENCES feeders(id),
    CONSTRAINT fk_feedcomments_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_feedcomments_parent FOREIGN KEY (parent_id) REFERENCES feed_comments(id)
);


CREATE TABLE IF NOT EXISTS comment_likes (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    comment_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_commentlikes_comment FOREIGN KEY (comment_id) REFERENCES feed_comments(id),
    CONSTRAINT fk_commentlikes_user FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY unique_comment_like (comment_id, user_id)
);




CREATE TABLE IF NOT EXISTS groups (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    owner_id BIGINT NOT NULL,              -- user who created the group
    name VARCHAR(255) NOT NULL,
    description TEXT,
    privacy ENUM('public','private','secret') DEFAULT 'public',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_groups_owner FOREIGN KEY (owner_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS group_members (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    group_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    role ENUM('member','admin','moderator') DEFAULT 'member',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_groupmembers_group FOREIGN KEY (group_id) REFERENCES groups(id),
    CONSTRAINT fk_groupmembers_user FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY unique_member (group_id, user_id)
);

CREATE TABLE IF NOT EXISTS pages (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    owner_id BIGINT NOT NULL,              -- user or organization
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pages_owner FOREIGN KEY (owner_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS page_followers (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    page_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    followed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pagefollowers_page FOREIGN KEY (page_id) REFERENCES pages(id),
    CONSTRAINT fk_pagefollowers_user FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY unique_follower (page_id, user_id)
);

CREATE TABLE IF NOT EXISTS events (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    creator_id BIGINT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    location VARCHAR(255),
    start_time DATETIME NOT NULL,
    end_time DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_events_creator FOREIGN KEY (creator_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS event_attendees (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    status ENUM('going','interested','not_going') DEFAULT 'interested',
    responded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_eventattendees_event FOREIGN KEY (event_id) REFERENCES events(id),
    CONSTRAINT fk_eventattendees_user FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY unique_attendee (event_id, user_id)
);

CREATE TABLE IF NOT EXISTS market_listings (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    seller_id BIGINT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'USD',
    media_url JSON DEFAULT (JSON_OBJECT()),
    category VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,
    CONSTRAINT fk_market_seller FOREIGN KEY (seller_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS market_orders (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    listing_id BIGINT NOT NULL,
    buyer_id BIGINT NOT NULL,
    status ENUM('pending','completed','cancelled') DEFAULT 'pending',
    ordered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_marketorders_listing FOREIGN KEY (listing_id) REFERENCES market_listings(id),
    CONSTRAINT fk_marketorders_buyer FOREIGN KEY (buyer_id) REFERENCES users(id)
);


CREATE TABLE IF NOT EXISTS friends (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    friend_id BIGINT NOT NULL,
    status ENUM('pending','accepted','blocked') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_friendship (user_id, friend_id),
    CONSTRAINT fk_friend_user FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_friend_friend FOREIGN KEY (friend_id) REFERENCES users(id)
        ON DELETE CASCADE ON UPDATE CASCADE
);
