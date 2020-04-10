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

interface CarMapperInterface
{
    /**
     * Returns total number of cars
     * 
     * @return int
     */
    public function getTotalCount();

    /**
     * Save service relation with current Car Id
     * 
     * @param int $carId
     * @param array $serviceIds
     * @return boolean
     */
    public function saveServiceRelation($carId, array $serviceIds);

    /**
     * Fetch all cars
     * 
     * @param int $page Optional page number
     * @param int $limit Optional per page limit
     * @return array
     */
    public function fetchAll($page = null, $limit = null);

    /**
     * Fetches car data by its associated id
     * 
     * @param string $id Car id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return array
     */
    public function fetchById($id, $withTranslations);
}
