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

use Cms\Storage\MySQL\AbstractMapper;;
use Rentcar\Storage\ServiceMapperInterface;

final class ServiceMapper extends AbstractMapper implements ServiceMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_rentcar_services');
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationTable()
    {
        return ServiceTranslationMapper::getTableName();
    }

    /**
     * Returns shared columns to be selected
     * 
     * @return array
     */
    private function getColumns()
    {
        return array(
            self::column('id'),
            self::column('order'),
            self::column('price'),
            ServiceTranslationMapper::column('lang_id'),
            ServiceTranslationMapper::column('name'),
            ServiceTranslationMapper::column('description')
        );
    }
}
