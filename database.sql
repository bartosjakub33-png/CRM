CREATE DATABASE IF NOT EXISTS  crm_gardens
    CHARACTER SET utf8mb4                            -- umožní ukladať aj špeciálne znaky, napr. č, š, ž
    COLLATE utf8mb4_unicode_ci;                      -- Určuje pravidlá porovnávania a zoraďovania textov,
                                                     -- napr. pri ORDER BY; ci = nerozlišuje veľké a malé písmená.
USE crm_gardens;

CREATE TABLE IF NOT EXISTS customers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,      -- jedinečný identifikátor zákazníka, každý riadok sa zväčšuje
    first_name VARCHAR (80) NOT NULL,                      -- meno zákazníka, max. 80 znakov, nesmie byť NULL
    last_name VARCHAR (80) NOT NULL,                -- priezvisko zákazníka, max. 80 znakov, nesmie byť NULL
    phone VARCHAR (30) NOT NULL,            -- telefónne číslo zákazníka, max. 30 znakov, nesmie byť NULL
    email VARCHAR (160) NULL UNIQUE,                 -- email zákazníka, max. 160 znakov, môže byť NULL, email musí byť jedinečný (nemôžu byť 2 ľudia pod tým istým menom)
    street VARCHAR (160) NULL,                        -- ulica zákazníka, max. 160 znakov, môže byť NULL
    city VARCHAR (100) NULL,                        -- mesto zákazníka, max. 100 znakov, môže byť NULL
    postal_code VARCHAR (12) NULL,                           -- poštové smerovacie číslo zákazníka, max. 12 znakov, môže byť NULL
    notes TEXT NULL,                              -- poznámky k zákazníkovi, môže byť NULL
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP    -- dátum a čas kedy bol záznam vytvorený, predvolená hodnota je aktuálny čas
) ENGINE=InnoDB;                                     -- zaručí že tabuľka bude podporovať všetky potrebné funkcie, napr. cudzí kľúč, transakcie, atď.

CREATE TABLE IF NOT EXISTS gardens (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,      
    customer_id INT UNSIGNED NOT NULL,               -- cudzí kľúč, ktorý odkazuje na zákazníka, nesmie byť NULL
    garden_name VARCHAR (120) NOT NULL DEFAULT 'Main Garden',  -- názov záhrady, max. 120 znakov, predvolená hodnota je 'Main Garden'
    zone_count SMALLINT UNSIGNED NOT NULL DEFAULT 1,  -- počet zón v záhrade, nesmie byť NULL, predvolená hodnota je 1
    sprinkler_type VARCHAR (120) NULL,             -- typ postrekovača, max. 120 znakov, môže byť NULL
    controller_model VARCHAR (120) NULL,                -- model riadiacej jednotky, max. 120 znakov, môže byť NULL
    valve_info VARCHAR (255) NULL,             -- informácie o ventiloch, max. 255 znakov, môže byť NULL
    water_source VARCHAR (80) NULL,                    -- zdroj vody pre záhradu, max. 80 znakov, môže byť NULL
    notes TEXT NULL,                              -- poznámky k záhrade, môže byť NULL
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,   -- dátum a čas kedy bol záznam vytvorený, predvolená hodnota je aktuálny čas
    CONSTRAINT fk_gardens_customers                   -- definícia cudzieho kľúča na tabuľku customers
      FOREIGN KEY (customer_id) REFERENCES customers(id)
      ON DELETE CASCADE                              -- pri vymazaní zákazníka sa vymažú aj jeho záhrady
)ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS service_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,      
    customer_id INT UNSIGNED NOT NULL,               -- cudzí kľúč, ktorý odkazuje na zákazníka, nesmie byť NULL
    title VARCHAR (160) NOT NULL,                    -- názov požiadavky, max. 160 znakov, nesmie byť NULL
    description TEXT NOT NULL,                             -- popis požiadavky,nemá max. počet znakov, nesmie byť NULL
    priority ENUM('Low','Normal','High','Urgent') NOT NULL DEFAULT 'Normal',  -- priorita požiadavky, predvolená hodnota je 'Normal'
    status ENUM('New','Scheduled','In Progress','Resolved','Cancelled') NOT NULL DEFAULT 'New',  -- stav požiadavky, predvolená hodnota je 'New'
    request_date DATE NULL,                        -- dátum kedy bola požiadavka vytvorená, môže byť NULL
    preferred_date DATE NULL,                     -- preferovaný dátum splnenia požiadavky, môže byť NULL
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,   -- dátum a čas kedy bol záznam vytvorený, predvolená hodnota je aktuálny čas
    CONSTRAINT fk_service_requests_customers                -- definícia cudzieho kľúča na tabuľku customers
      FOREIGN KEY (customer_id) REFERENCES customers(id)
      ON DELETE CASCADE                              -- pri vymazaní zákazníka sa vymažú aj jeho požiadavky na služby
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS service_visits (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,      
    request_id INT UNSIGNED NOT NULL,             -- cudzí kľúč, ktorý odkazuje na požiadavku na službu, nesmie byť NULL
    visit_date DATETIME NOT NULL,                -- dátum a čas návštevy, nesmie byť NULL
    assigned_to VARCHAR (120) NULL,               -- meno osoby, ku ktorej je návšteva priradená, max. 120 znakov, môže byť NULL
    work_done TEXT NULL,                        -- popis vykonanej práce, nemá max. počet znakov, môže byť NULL
    materials_used TEXT NULL,                     -- zoznam použitých materiálov, nemá max. počet znakov, môže byť NULL
    notes TEXT NULL,                              -- poznámky k návšteve, nemá max. počet znakov, môže byť NULL
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,   -- dátum a čas kedy bol záznam vytvorený, predvolená hodnota je aktuálny čas
    CONSTRAINT fk_service_visits_requests                -- definícia cudzieho kľúča na tabuľku service_requests
      FOREIGN KEY (request_id) REFERENCES service_requests(id)
      ON DELETE CASCADE                              -- pri vymazaní požiadavky sa vymažú aj jej návštevy
) ENGINE=InnoDB;

CREATE USER 'crm_user'@'localhost' IDENTIFIED BY 'silne_heslo';
GRANT ALL PRIVILEGES ON crm_gardens.* TO 'crm_user'@'localhost';
FLUSH PRIVILEGES;