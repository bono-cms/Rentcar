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
     * Returns total count
     * 
     * @return int
     */
    public function getTotalCarCount();

    /**
     * Takes count of 
     * 
     * @param string $datetime
     * @return int
     */
    public function getTakenCount($datetime);

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
     * Finds transaction by its associated token
     * 
     * @param string $token
     * @return array
     */
    public function findByToken($token);

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
     * Updates transaction status by its token
     * 
     * @param string $token Unique transaction token
     * @param int $status New status constant
     * @return boolean Depending on success
     */
    public function updateStatusByToken($token, $status);

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
