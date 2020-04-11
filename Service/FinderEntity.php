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

use Krystal\Stdlib\VirtualEntity;

final class FinderEntity extends VirtualEntity
{
    /**
     * Checks if entire entity is populated
     * 
     * @return boolean
     */
    public function isPopulated()
    {
        $filtered = array_filter($this->getProperties(), function($value){
            return empty($value);
        });

        return empty($filtered);
    }
}
