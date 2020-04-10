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

use Cms\Storage\MySQL\AbstractStorageDropper;

final class Dropper extends AbstractStorageDropper
{
    /**
     * {@inheritDoc}
     */
    protected function getTables()
    {
        return array(
            CarMapper::getTableName(),
            CarTranslationMapper::getTableName(),
            CarGalleryMapper::getTableName(),
            BrandMapper::getTableName(),
            LeaseMapper::getTableName(),
            CarModificationMapper::getTableName(),
            CarModificationTranslationMapper::getTableName(),

            // Extra services
            RentServiceMapper::getTableName(),
            RentServiceTranslationMapper::getTableName(),
            RentServiceRelationMapper::getTableName(),

            // Booking
            BookingMapper::getTableName()
        );
    }
}
