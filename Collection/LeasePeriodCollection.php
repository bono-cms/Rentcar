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

final class LeasePeriodCollection extends ArrayCollection
{
    /**
     * {@inheritDoc}
     */
    protected $collection = array(
        12 => '1 Year (12 months)',
        24 => '2 Years (24 months)',
        36 => '3 Years (36 months)',
        48 => '4 Years (48 months)',
        60 => '5 Years (60 months)'
    );
}
