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
     * Count statuses
     * 
     * @return array
     */
    public function getStatusSummary();

    /**
     * Counts total sum with corresponding currencies
     * 
     * @return array
     */
    public function getAmountSummary();

    /**
     * Fetch cars with booking status
     * 
     * @param string $datetime
     * @return array
     */
    public function fetchCars($datetime);

    /**
     * Get car availability information
     * 
     * @param int $carId
     * @param string $checkin
     * @param string $checkout
     * @return array
     */
    public function carAvailability($carId, $checkin, $checkout);

    /**
     * Update booking status
     * 
     * @param int $id Booking id
     * @param int $status Status constant
     * @return boolean
     */
    public function updateStatus($id, $status);

    /**
     * Count new orders
     * 
     * @return int
     */
    public function countNew();

    /**
     * {@inheritDoc}
     */
    public function filter($input, $page, $itemsPerPage, $sortingColumn, $desc, array $parameters = []);
}
