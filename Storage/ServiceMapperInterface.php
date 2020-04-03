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

interface ServiceMapperInterface
{
    /**
     * Fetch all extra services
     * 
     * @param boolean $sort Whether to sort by order
     * @return array
     */
    public function fetchAll($sort = false);

    /**
     * Fetches a service by its id
     * 
     * @param int $id Service id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return mixed
     */
    public function fetchById($id, $withTranslations);
}
