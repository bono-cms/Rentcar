
DROP TABLE IF EXISTS `bono_module_rentcar_lease`;
CREATE TABLE `bono_module_rentcar_lease` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `owner` varchar(255) NOT NULL COMMENT 'Owner name',
    `model` varchar(255) NOT NULL COMMENT 'Car model',
    `numberplate` varchar(255) NOT NULL COMMENT 'Car Numberplate',
    `contract_number` varchar(255) NOT NULL COMMENT 'Contract number',
    `apply_date` DATE NOT NULL COMMENT 'Date of applying',
    `run_date` DATE NOT NULL COMMENT 'Date of receiving car',
    `contract_lease_number` varchar(255) NOT NULL COMMENT 'Contract and lease number',
    `period` INT NOT NULL COMMENT 'Contract period',
    `status` INT NOT NULL COMMENT 'Contract status',
    `city_applied` varchar(255) NOT NULL COMMENT 'City where it has been applied',
    `city_owner` varchar(255) NOT NULL COMMENT 'City of owner',
    `comment` TEXT NOT NULL COMMENT 'Extra information'
);

DROP TABLE IF EXISTS `bono_module_rentcar_brands`;
CREATE TABLE `bono_module_rentcar_brands` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `order` INT NOT NULL COMMENT 'Sorting order',
    `name` varchar(255) NOT NULL COMMENT 'Brand name',
    `icon` varchar(255) NOT NULL COMMENT 'Brand icon'
);

DROP TABLE IF EXISTS `bono_module_rentcar_cars`;
CREATE TABLE bono_module_rentcar_cars (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `brand_id` INT DEFAULT NULL,
    `order` INT NOT NULL COMMENT 'Sorting order'
);

DROP TABLE IF EXISTS `bono_module_rentcar_cars_translations`;
CREATE TABLE `bono_module_rentcar_cars_translations` (
    `id` INT NOT NULL,
    `lang_id` INT NOT NULL COMMENT 'Language identificator',
    `web_page_id` INT NOT NULL COMMENT 'Web page identificator',
    `name` varchar(255) NOT NULL,
    `description` TEXT NOT NULL,
    `interior` TEXT NOT NULL,
    `exterior` TEXT NOT NULL,

    FOREIGN KEY (id) REFERENCES bono_module_rentcar_cars(id) ON DELETE CASCADE
);
