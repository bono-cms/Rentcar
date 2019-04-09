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
use Rentcar\Storage\CarMapperInterface;

final class CarMapper extends AbstractMapper implements CarMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_rentcar_cars');
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationTable()
    {
        return CarTranslationMapper::getTableName();
    }
}
