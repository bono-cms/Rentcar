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
use Cms\Storage\MySQL\WebPageMapper;
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
            self::column('brand_id'),
            self::column('price'),
            self::column('order'),
            self::column('image'),
            self::column('qty'),
            CarTranslationMapper::column('lang_id'),
            CarTranslationMapper::column('web_page_id'),
            CarTranslationMapper::column('name'),
            CarTranslationMapper::column('interior'),
            CarTranslationMapper::column('exterior'),
            CarTranslationMapper::column('features'),
            CarTranslationMapper::column('options'),
            CarTranslationMapper::column('description'),
            /* Common front attributes */
            CarTranslationMapper::column('capacity'),
            CarTranslationMapper::column('transmission'),
            CarTranslationMapper::column('safety'),
            CarTranslationMapper::column('fuel'),
            CarTranslationMapper::column('airbags'),
            /* SEO-related attributes */
            CarTranslationMapper::column('title'),
            CarTranslationMapper::column('keywords'),
            CarTranslationMapper::column('meta_description'),

            WebPageMapper::column('slug'),
            WebPageMapper::column('changefreq'),
            WebPageMapper::column('priority')
        );
    }

    /**
     * Save service relation with current Car Id
     * 
     * @param int $carId
     * @param array $serviceIds
     * @return boolean
     */
    public function saveServiceRelation($carId, array $serviceIds)
    {
        return $this->syncWithJunction(RentServiceRelationMapper::getTableName(), $carId, $serviceIds);
    }

    /**
     * Fetch all cars
     * 
     * @param int $page Optional page number
     * @param int $limit Optional per page limit
     * @return array
     */
    public function fetchAll($page = null, $limit = null)
    {
        $columns = $this->getColumns();
        $columns[BrandMapper::column('name')] = 'brand';

        $db = $this->createWebPageSelect($columns)
                   // Brand relation
                   ->leftJoin(BrandMapper::getTableName(), array(
                        BrandMapper::column('id') => self::column('brand_id')
                   ))
                   ->whereEquals(CarTranslationMapper::column('lang_id'), $this->getLangId())
                   ->orderBy(self::column('id'));

        // Apply pagination if required
        if ($page !== null && $limit !== null) {
            $db->paginate($page, $limit);
        }

        // Limit rows
        if ($page === null && $limit !== null){
            $db->limit($limit);
        }

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
        return $this->findWebPage($this->getColumns(), $id, $withTranslations);
    }
}
