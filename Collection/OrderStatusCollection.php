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
    const STATUS_NEW = 2;
    const STATUS_VOID = 3;

    /**
     * {@inheritDoc}
     */
    protected $collection = [
        self::STATUS_DECLINED => 'Declined',
        self::STATUS_APPROVED => 'Approved',
        self::STATUS_NEW => 'New',
        self::STATUS_VOID => 'Void'
    ];

    /**
     * Return payment statuses except void state
     * 
     * @return array
     */
    public function getStatuses()
    {
        $collection = $this->collection;

        // Remove void state
        unset($collection[self::STATUS_VOID]);

        return $collection;
    }
}
