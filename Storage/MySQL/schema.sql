
DROP TABLE IF EXISTS `bono_module_rentcar_cars`;
CREATE TABLE bono_module_rentcar_cars (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `order` INT NOT NULL COMMENT 'Sorting order'
);

DROP TABLE IF EXISTS `bono_module_rentcar_cars_translations`;
CREATE TABLE `bono_module_rentcar_cars_translations` (
    `id` INT NOT NULL,
    `lang_id` INT NOT NULL COMMENT 'Language identificator',
    `name` varchar(255) NOT NULL,
    `description` TEXT NOT NULL,
    `interior` TEXT NOT NULL,
    `exterior` TEXT NOT NULL,

    FOREIGN KEY (id) REFERENCES bono_module_rentcar_cars(id) ON DELETE CASCADE
);
