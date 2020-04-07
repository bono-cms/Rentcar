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

final class OrderStatusCollection extends ArrayCollection
{
    /* Status constants */
    const STATUS_DECLINED = 0;
    const STATUS_APPROVED = 1;

    /**
     * {@inheritDoc}
     */
    protected $collection = [
        self::STATUS_DECLINED => 'Declined',
        self::STATUS_APPROVED => 'Approved'
    ];
}
