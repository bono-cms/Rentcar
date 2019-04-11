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

    /**
     * Returns columns to be selected
     * 
     * @return array
     */
    private function getColumns()
    {
        return array(
            self::column('id'),
            self::column('order'),
            CarTranslationMapper::column('lang_id'),
            CarTranslationMapper::column('name'),
            CarTranslationMapper::column('interior'),
            CarTranslationMapper::column('exterior'),
            CarTranslationMapper::column('description')
        );
    }

    /**
     * Fetch all cars
     * 
     * @return array
     */
    public function fetchAll()
    {
        $db = $this->createEntitySelect($this->getColumns())
                   ->whereEquals(CarTranslationMapper::column('lang_id'), $this->getLangId())
                   ->orderBy(self::column('id'));

        return $db->queryAll();
    }

    /**
     * Fetches car data by its associated id
     * 
     * @param string $id Car id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return array
     */
    public function fetchById($id, $withTranslations)
    {
        return $this->findEntity($this->getColumns(), $id, $withTranslations);
    }
}
