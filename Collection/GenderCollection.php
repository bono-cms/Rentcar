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

final class GenderCollection extends ArrayCollection
{
    /**
     * {@inheritDoc}
     */
    protected $collection = [
        0 => 'Male',
        1 => 'Female'
    ];
}
