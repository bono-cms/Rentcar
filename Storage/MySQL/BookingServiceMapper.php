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
use Rentcar\Storage\BookingServiceMapperInterface;

final class BookingServiceMapper extends AbstractMapper implements BookingServiceMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_rentcar_booking_services');
    }

    /**
     * Fetch all booking services by associated booking id
     * 
     * @param int $bookingId
     * @return array
     */
    public function fetchAll($bookingId)
    {
        // Columns to be selected
        $columns = [
            RentServiceTranslationMapper::column('name') => 'service',
            self::column('price'),
            BookingMapper::column('currency')
        ];

        $db = $this->db->select($columns)
                       ->from(self::getTableName())
                       ->leftJoin(RentServiceMapper::getTableName(), [
                            RentServiceMapper::column('id') => self::getRawColumn('service_id')
                       ])
                       ->leftJoin(RentServiceTranslationMapper::getTableName(), [
                            RentServiceTranslationMapper::column('id') => RentServiceMapper::getRawColumn('id')
                       ])
                       // Booking relation
                       ->leftJoin(BookingMapper::getTableName(), [
                            BookingMapper::column('id') => self::getRawColumn('booking_id')
                       ])
                       ->whereEquals(RentServiceTranslationMapper::column('lang_id'), $this->getLangId())
                       ->andWhereEquals(self::column('booking_id'), $bookingId)
                       ->orderBy(self::getRawColumn('id'))
                       ->desc();

        return $db->queryAll();
    }
}
