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
use Rentcar\Storage\CarModificationMapperInterface;

final class CarModificationMapper extends AbstractMapper implements CarModificationMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_rentcar_cars_modifications');
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationTable()
    {
        return CarModificationTranslationMapper::getTableName();
    }
}
