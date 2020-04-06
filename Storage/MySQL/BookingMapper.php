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
use Rentcar\Storage\BookingMapperInterface;

final class BookingMapper extends AbstractMapper implements BookingMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_rentcar_booking');
    }

    /**
     * Fetch all bookings
     * 
     * @param int $page Current page number
     * @param int $limit Per page limit
     * @return array
     */
    public function fetchAll($page = null, $limit = null)
    {
        // Columns to be selected
        $columns = [
            self::column('id'),
            self::column('car_id'),
            // Main details
            self::column('status'),
            self::column('amount'),
            self::column('datetime'),
            // Client details
            self::column('name'),
            self::column('email'),
            self::column('phone'),
            self::column('gender'),
            self::column('comment'),
            // Order detauls
            self::column('pickup'),
            self::column('return'),
            self::column('checkin'),
            self::column('checkout'),

            CarMapper::column('image') => 'image',
            CarTranslationMapper::column('name') => 'car'
        ];

        $db = $this->db->select($columns)
                       ->from(self::getTableName())
                       // Car relation
                       ->leftJoin(CarMapper::getTableName(), [
                            CarMapper::column('id') => self::getRawColumn('car_id')
                       ])
                       // Car translation relation
                       ->leftJoin(CarTranslationMapper::getTableName(), [
                            CarTranslationMapper::column('id') => CarMapper::getRawColumn('id')
                       ])
                       // Language constraint
                       ->whereEquals(CarTranslationMapper::column('lang_id'), $this->getLangId())
                       ->orderBy(self::column('id'))
                       ->desc();

        // Apply pagination if required
        if ($page !== null && $limit !== null) {
            $db->paginate($page, $limit);
        }

        return $db->queryAll();
    }
}
