<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Rentcar\Storage;

interface BookingMapperInterface
{
    /**
     * Fetch all bookings
     * 
     * @param int $page Current page number
     * @param int $limit Per page limit
     * @return array
     */
    public function fetchAll($page = null, $limit = null);
}
