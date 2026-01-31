-- Role: DBA
-- Task: Generate Normalized Schema with Constraints & Indexes
-- Version: 1.0

-- -----------------------------------------------------
-- Table: users
-- -----------------------------------------------------
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_users_email (email)
);

-- -----------------------------------------------------
-- Table: restaurants
-- -----------------------------------------------------
CREATE TABLE restaurants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    owner_id BIGINT UNSIGNED, -- Linked logically via Application Logic or future FK
    uuid CHAR(36) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    is_active BOOLEAN DEFAULT TRUE,
    
    -- JSON Columns for Translations & Config (Hybrid Approach)
    name_translations JSON, 
    description_translations JSON,
    settings JSON, -- Stores 'ai_agent_config', 'hours', etc.
    
    city VARCHAR(100) NOT NULL,
    
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL, -- Soft Delete

    -- Indexes for Performance
    INDEX idx_restaurants_slug (slug), -- Critical for Public Page / SEO lookup
    INDEX idx_restaurants_uuid (uuid),
    INDEX idx_restaurants_active_city (is_active, city) -- Composite for "Find Restaurants in City"
);

-- Note: owner relationship is managed via roles in code, but pure SQL linkage:
-- ALTER TABLE restaurants ADD CONSTRAINT fk_rest_owner FOREIGN KEY (owner_id) REFERENCES users(id);

-- -----------------------------------------------------
-- Table: menus
-- -----------------------------------------------------
CREATE TABLE menus (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    restaurant_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    name_translations JSON,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    CONSTRAINT fk_menus_restaurant FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE,
    INDEX idx_menus_rest_active (restaurant_id, is_active)
);

-- -----------------------------------------------------
-- Table: categories
-- -----------------------------------------------------
CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    menu_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    name_translations JSON,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    CONSTRAINT fk_cats_menu FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE CASCADE,
    INDEX idx_cats_menu_order (menu_id, sort_order)
);

-- -----------------------------------------------------
-- Table: products
-- -----------------------------------------------------
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    name_translations JSON,
    description_translations JSON,
    price DECIMAL(10, 2) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    is_available BOOLEAN DEFAULT TRUE, -- Out of stock flag
    image VARCHAR(255),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    CONSTRAINT fk_prods_cat FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_prods_cat_active (category_id, is_active)
);

-- -----------------------------------------------------
-- Table: conversations (Analytics / Logs)
-- -----------------------------------------------------
CREATE TABLE conversations (
    id CHAR(36) PRIMARY KEY, -- UUID
    restaurant_id BIGINT UNSIGNED NOT NULL,
    customer_identifier VARCHAR(255), -- Hash of Phone Number
    message_direction ENUM('inbound', 'outbound') NOT NULL,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sentiment VARCHAR(50), -- 'positive', 'negative', 'neutral'
    
    CONSTRAINT fk_conv_rest FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE,
    INDEX idx_conv_rest_date (restaurant_id, started_at) -- For "Monthly Conversations" Reporting
);
