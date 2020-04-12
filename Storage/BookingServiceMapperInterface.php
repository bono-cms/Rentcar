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

interface BookingServiceMapperInterface
{
    /**
     * Fetch all booking services by associated booking id
     * 
     * @param int $bookingId
     * @return array
     */
    public function fetchAll($bookingId);
}
