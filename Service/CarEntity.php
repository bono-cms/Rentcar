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

final class CarEntity extends VirtualEntity
{
    /**
     * Returns car image
     * 
     * @param string $size Image size
     * @return string
     */
    public function getImageUrl($size)
    {
        return $this->getImageBag()->getUrl($size);
    }
}
