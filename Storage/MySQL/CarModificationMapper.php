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
            CarModificationTranslationMapper::column('lang_id'),
            CarModificationTranslationMapper::column('name')
        );
    }

    /**
     * Fetch all prices
     * 
     * @param mixed $carId Optional car ID constraint
     * @return array
     */
    public function fetchAllPrices($carId = null)
    {
        $columns = array(
            CarMapper::column('id'),
            CarMapper::column('price'),
            CarTranslationMapper::column('name') => 'car'
        );

        $db = $this->db->select($columns)
                       ->from(CarMapper::getTableName())
                       // Translation mapper
                       ->leftJoin(CarTranslationMapper::getTableName(), array(
                            CarTranslationMapper::column('id') => CarMapper::getRawColumn('id')
                       ))
                       ->whereEquals(CarTranslationMapper::column('lang_id'), $this->getLangId());

        // Apply car ID constraint if provided
        if ($carId !== null) {
            $db->andWhereEquals(CarMapper::column('id'), $carId);
        }

        $db->orderBy(CarMapper::column('id'))
           ->desc();

        return $db->queryAll();
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
     * @param mixed $carId
     * @return array
     */
    public function fetchAll($carId = null)
    {
        $columns = $this->getColumns();
        $columns[CarTranslationMapper::column('name')] = 'car';

        $db = $this->createEntitySelect($columns)
                   // Car relation
                   ->leftJoin(CarMapper::getTableName(), array(
                        CarMapper::column('id') => self::getRawColumn('car_id')
                   ))
                   // Car translation mapper
                   ->leftJoin(CarTranslationMapper::getTableName(), array(
                        CarTranslationMapper::column('id') => CarMapper::getRawColumn('id'),
                        CarTranslationMapper::column('lang_id') => CarModificationTranslationMapper::getRawColumn('lang_id')
                   ))
                   ->whereEquals(CarModificationTranslationMapper::column('lang_id'), $this->getLangId());

        // Apply car ID constraint if provided
        if ($carId !== null) {
            $db->andWhereEquals(self::column('car_id'), $carId);
        }

        $db->orderBy(self::column('id'))
           ->desc();

        return $db->queryAll();
    }
}
