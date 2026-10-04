<?php

/**
 * This file is part of the Bono CMS
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Rentcar\Storage;

interface LeaseMapperInterface
{
    /**
     * Fetch all lease items
     * 
     * @return array
     */
    public function fetchAll();
}
