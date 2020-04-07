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
use Rentcar\Collection\OrderStatusCollection;

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
     * Update booking status
     * 
     * @param int $id Booking id
     * @param int $status STatus constant
     * @return boolean
     */
    public function updateStatus($id, $status)
    {
        $db = $this->db->update(self::getTableName(), ['status' => $status])
                       ->whereEquals('id', $id);

        return (bool) $db->execute(true);
    }

    /**
     * Count new orders
     * 
     * @return int
     */
    public function countNew()
    {
        $db = $this->db->select()
                       ->count('id')
                       ->from(self::getTableName())
                       ->whereEquals('status', OrderStatusCollection::STATUS_NEW);

        return $db->queryScalar();
    }

    /**
     * {@inheritDoc}
     */
    public function filter($input, $page, $itemsPerPage, $sortingColumn, $desc, array $parameters = [])
    {
        $sortingColumns = [
            'car_id' => self::column('car_id'),
            'status' => self::column('status'),
            'client' => self::column('client')
        ];

        // Current sorting column
        $sortingColumn = isset($sortingColumns[$sortingColumn]) ? $sortingColumns[$sortingColumn] : self::column($this->getPk());

        // If not defined explicitly, sort by PK
        if (!$sortingColumn) {
            $sortingColumn = $this->getPk();
        }

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
            // Order details
            self::column('pickup'),
            self::column('return'),
            self::column('checkin'),
            self::column('checkout'),
            // The rest
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

                       // Filter constraints
                       ->andWhereEquals(self::column('car_id'), $input['car_id'], true)
                       ->andWhereEquals(self::column('status'), $input['status'], true)
                       ->andWhereLike(self::column('name'), '%'.$input['name'].'%', true);

        // Apply sorting
        $db->orderBy($sortingColumn);

        if ($desc) {
            $db->desc();
        }

        // Apply pagination if required
        if ($page !== null && $itemsPerPage !== null) {
            $db->paginate($page, $itemsPerPage);
        }

        return $db->queryAll();        
    }
}
