<?php

/**
 * This file is part of the Bono CMS
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
    protected $collection = [
        12 => '12 months',
        24 => '24 months',
        36 => '36 months',
        48 => '48 months',
        60 => '60 months'
    ];
}
