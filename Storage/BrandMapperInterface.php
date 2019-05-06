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

interface BrandMapperInterface
{
    /**
     * Fetch all brands
     * 
     * @param boolean $sort Whether to sort brands by their sorting order
     * @return array
     */
    public function fetchAll($sort);
}
