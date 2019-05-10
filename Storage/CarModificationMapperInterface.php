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

interface CarModificationMapperInterface
{
    /**
     * Fetch all modifications by associated car id
     * 
     * @param int $carId
     * @return array
     */
    public function fetchAll($carId);
}
