CREATE DATABASE IF NOT EXISTS crm_gardens
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE crm_gardens;

CREATE TABLE IF NOT EXISTS customers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(80) NOT NULL,
    last_name VARCHAR(80) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    email VARCHAR(160) NULL UNIQUE,
    street VARCHAR(160) NOT NULL,
    city VARCHAR(100) NOT NULL,
    postal_code VARCHAR(12) NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS gardens (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    garden_name VARCHAR(120) NOT NULL DEFAULT 'Hlavná záhrada',
    zone_count SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    sprinkler_type VARCHAR(120) NULL,
    controller_model VARCHAR(120) NULL,
    valve_info VARCHAR(255) NULL,
    water_source VARCHAR(80) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_gardens_customers
      FOREIGN KEY (customer_id) REFERENCES customers(id)
      ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS service_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    title VARCHAR(160) NOT NULL,
    description TEXT NOT NULL,
    priority ENUM('Nízka','Normálna','Vysoká','Urgentná') NOT NULL DEFAULT 'Normálna',
    status ENUM('Nová','Naplánovaná','V riešení','Vyriešená','Zrušená') NOT NULL DEFAULT 'Nová',
    request_date DATE NULL,
    preferred_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_service_requests_customers
      FOREIGN KEY (customer_id) REFERENCES customers(id)
      ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS service_visits (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_id INT UNSIGNED NOT NULL,
    visit_date DATETIME NOT NULL,
    assigned_to VARCHAR(120) NULL,
    work_done TEXT NULL,
    materials_used TEXT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_service_visits_requests
      FOREIGN KEY (request_id) REFERENCES service_requests(id)
      ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE USER 'crm_user'@'localhost' IDENTIFIED BY 'silne_heslo';
GRANT ALL PRIVILEGES ON crm_gardens.* TO 'crm_user'@'localhost';
FLUSH PRIVILEGES;