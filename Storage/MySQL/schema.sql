
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
) DEFAULT CHARSET=UTF8 ENGINE = InnoDB;

DROP TABLE IF EXISTS `bono_module_rentcar_brands`;
CREATE TABLE `bono_module_rentcar_brands` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `order` INT NOT NULL COMMENT 'Sorting order',
    `name` varchar(255) NOT NULL COMMENT 'Brand name',
    `icon` varchar(255) NOT NULL COMMENT 'Brand icon'
) DEFAULT CHARSET=UTF8 ENGINE = InnoDB;

DROP TABLE IF EXISTS `bono_module_rentcar_cars`;
CREATE TABLE bono_module_rentcar_cars (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `brand_id` INT DEFAULT NULL,
    `price` FLOAT NOT NULL COMMENT 'Car price',
    `order` INT NOT NULL COMMENT 'Sorting order',
    `image` varchar(255) NOT NULL COMMENT 'Car cover image',
    `qty` INT NOT NULL COMMENT 'Total number of available cars. 0 - Unlimited',
    `rent` FLOAT NOT NULL COMMENT 'Daily rent price'

    FOREIGN KEY (brand_id) REFERENCES bono_module_rentcar_brands(id) ON DELETE CASCADE
) DEFAULT CHARSET=UTF8 ENGINE = InnoDB;

DROP TABLE IF EXISTS `bono_module_rentcar_cars_translations`;
CREATE TABLE `bono_module_rentcar_cars_translations` (
    `id` INT NOT NULL,
    `lang_id` INT NOT NULL COMMENT 'Language identificator',
    `web_page_id` INT NOT NULL COMMENT 'Web page identificator',
    `name` varchar(255) NOT NULL,
    `description` TEXT NOT NULL,
    `interior` TEXT NOT NULL,
    `exterior` TEXT NOT NULL,
    `features` TEXT NOT NULL,
    `options` TEXT NOT NULL,

    /* Common front attributes */
    `capacity` varchar(255) NOT NULL,
    `transmission` varchar(255) NOT NULL,
    `safety` varchar(255) NOT NULL,
    `fuel` varchar(255) NOT NULL,
    `airbags` varchar(255) NOT NULL,

    /* SEO - related attributes */
    `title` varchar(255) NOT NULL,
    `keywords` TEXT NOT NULL,
    `meta_description` TEXT NOT NULL,

    FOREIGN KEY (id) REFERENCES bono_module_rentcar_cars(id) ON DELETE CASCADE,
    FOREIGN KEY (lang_id) REFERENCES bono_module_cms_languages(id) ON DELETE CASCADE,
    FOREIGN KEY (web_page_id) REFERENCES bono_module_cms_webpages(id) ON DELETE CASCADE
) DEFAULT CHARSET=UTF8 ENGINE = InnoDB;

/* Car modifications */
DROP TABLE IF EXISTS bono_module_rentcar_cars_modifications;
CREATE TABLE bono_module_rentcar_cars_modifications (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `car_id` INT NOT NULL COMMENT 'Related car ID',
    `price` FLOAT NOT NULL COMMENT 'Car price',

    FOREIGN KEY (car_id) REFERENCES bono_module_rentcar_cars(id) ON DELETE CASCADE
) DEFAULT CHARSET=UTF8 ENGINE = InnoDB;

/* Car modifications translations */
DROP TABLE IF EXISTS bono_module_rentcar_cars_modifications_translations;
CREATE TABLE bono_module_rentcar_cars_modifications_translations (
    `id` INT NOT NULL COMMENT 'Modification ID',
    `lang_id` INT NOT NULL COMMENT 'Language identificator',
    `name` varchar(255) NOT NULL COMMENT 'Modification name',

    FOREIGN KEY (id) REFERENCES bono_module_rentcar_cars_modifications(id) ON DELETE CASCADE,
    FOREIGN KEY (lang_id) REFERENCES bono_module_cms_languages(id) ON DELETE CASCADE
) DEFAULT CHARSET=UTF8 ENGINE = InnoDB;


/* Car extra services */
DROP TABLE IF EXISTS bono_module_rentcar_services;
CREATE TABLE  bono_module_rentcar_services (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Service ID',
    `order` INT NOT NULL COMMENT 'Sorting order',
    `price` FLOAT NOT NULL COMMENT 'Service price',
    `unit` TINYINT NOT NULL COMMENT 'Unit constant'
) DEFAULT CHARSET=UTF8 ENGINE = InnoDB;

DROP TABLE IF EXISTS bono_module_rentcar_services_translations;
CREATE TABLE bono_module_rentcar_services_translations (
    `id` INT NOT NULL COMMENT 'Service ID',
    `lang_id` INT NOT NULL COMMENT 'Language ID',
    `name` varchar(255) NOT NULL COMMENT 'Service name',
    `description` TEXT NOT NULL COMMENT 'Service description',

    FOREIGN KEY (id) REFERENCES bono_module_rentcar_services(id) ON DELETE CASCADE
) DEFAULT CHARSET=UTF8 ENGINE = InnoDB;

DROP TABLE IF EXISTS bono_module_rentcar_services_relation;
CREATE TABLE bono_module_rentcar_services_relation (
    `master_id` INT NOT NULL COMMENT 'Car ID',
    `slave_id` INT NOT NULL COMMENT 'Service ID',

    FOREIGN KEY (master_id) REFERENCES bono_module_rentcar_cars(id) ON DELETE CASCADE,
    FOREIGN KEY (slave_id) REFERENCES bono_module_rentcar_services(id) ON DELETE CASCADE
);

/* Car booking */
DROP TABLE IF EXISTS bono_module_rentcar_booking;
CREATE TABLE bono_module_rentcar_booking (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Booking ID',
    `car_id` INT NOT NULL COMMENT 'Attached Car ID',

    /* Main details */
    `status` SMALLINT NOT NULL COMMENT 'Booking status',
    `amount` FLOAT NOT NULL COMMENT 'Total amount',
    `datetime` DATETIME NOT NULL COMMENT 'Date and time or this order',
    `method` SMALLINT NOT NULL COMMENT 'Payment method',

    /* Client details */
    `name` varchar(255) NOT NULL COMMENT 'Client Full Name',
    `gender` TINYINT NOT NULL COMMENT 'Client gender',
    `email` varchar(255) NOT NULL COMMENT 'Client email',
    `phone` varchar(255) NOT NULL COMMENT 'Client phone',
    `comment` TEXT NOT NULL COMMENT 'Extra wishes',

    /* Order details */
    `pickup` TEXT NOT NULL COMMENT 'Pickup location',
    `return` TEXT NOT NULL COMMENT 'Return localtion',
    `checkin` DATETIME NOT NULL,
    `checkout` DATETIME NOT NULL,

    FOREIGN KEY (car_id) REFERENCES bono_module_rentcar_cars(id) ON DELETE CASCADE

) DEFAULT CHARSET=UTF8 ENGINE = InnoDB;
