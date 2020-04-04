<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Rentcar\Service;

use Rentcar\Storage\BookingMapperInterface;
use Cms\Service\AbstractManager;

final class BookingService extends AbstractManager
{
    /**
     * Booking mapper
     * 
     * @var \Rentcar\Storage\BookingMapperInterface
     */
    private $bookingMapper;

    /**
     * State initialization
     * 
     * @param \Rentcar\Storage\BookingMapperInterface $bookingMapper
     * @return void
     */
    public function __construct(BookingMapperInterface $bookingMapper)
    {
        $this->bookingMapper = $bookingMapper;
    }
}
