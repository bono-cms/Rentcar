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

final class StatusCollection extends ArrayCollection
{
    const STATUS_IN_PROGRESS = 1;
    const STATUS_FINISHED = 2;

    /**
     * {@inheritDoc}
     */
    protected $collection = array(
        self::STATUS_IN_PROGRESS => 'In progress',
        self::STATUS_FINISHED => 'Transfered to owner'
    );
}
