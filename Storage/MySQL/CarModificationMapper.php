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

    /**
     * Returns columns to be selected
     * 
     * @return array
     */
    private function getColumns()
    {
        return array(
            self::column('id'),
            self::column('car_id'),
            self::column('price'),
            CarModificationTranslationMapper::column('name')
        );
    }

    /**
     * Fetch car modification by its id
     * 
     * @param int $id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return array
     */
    public function fetchById($id, $withTranslations)
    {
        return $this->findEntity($this->getColumns(), $id, $withTranslations);
    }

    /**
     * Fetch all modifications by associated car id
     * 
     * @param int $carId
     * @return array
     */
    public function fetchAll($carId)
    {
        $db = $this->createEntitySelect($this->getColumns())
                   ->whereEquals(CarModificationTranslationMapper::column('lang_id'), $this->getLangId())
                   ->andWhereEquals(self::column('car_id'), $carId)
                   ->orderBy(self::column('id'))
                   ->desc();

        return $db->queryAll()
    }
}
