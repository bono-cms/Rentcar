<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Rentcar\Collection;

use Krystal\Stdlib\ArrayCollection;

final class RentServiceUnitCollection extends ArrayCollection
{
    /* Unit constants */
    const UNIT_DAILY = 1;
    const UNIT_ONCE = 2;

    /**
     * {@inheritDoc}
     */
    protected $collection = [
        self::UNIT_DAILY => 'Daily cost',
        self::UNIT_ONCE => 'One payment'
    ];
}
