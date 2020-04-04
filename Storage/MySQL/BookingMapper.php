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
        $db = $this->db->select('*')
                       ->from(self::getTableName())
                       ->orderBy('id')
                       ->desc();

        // Apply pagination if required
        if ($page !== null && $limit !== null) {
            $db->paginate($page, $limit);
        }

        return $db->queryAll();
    }
}
