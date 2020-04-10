<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Rentcar\Storage\MySQL;

use Cms\Storage\MySQL\AbstractMapper;
use Rentcar\Storage\CarGalleryMapperInterface;

final class CarGalleryMapper extends AbstractMapper implements CarGalleryMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_rentcar_cars_gallery');
    }

    /**
     * Fetch all images by associated car id
     * 
     * @param int $carId
     * @return array
     */
    public function fetchAll($carId)
    {
        $db = $this->db->select('*')
                       ->from(self::getTableName())
                       ->whereEquals('car_id', $carId)
                       ->orderBy('id')
                       ->desc();

        return $db->queryAll();
    }
}
