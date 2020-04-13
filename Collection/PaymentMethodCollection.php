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

final class PaymentMethodCollection extends ArrayCollection
{
    /* Method constants */
    const METHOD_CASH = 1;
    const METHOD_CARD = 2;
    const METHOD_BANK = 3;

    /**
     * {@inheritDoc}
     */
    protected $collection = [
        self::METHOD_CASH => 'By cash',
        self::METHOD_CARD => 'By card',
        self::METHOD_BANK => 'Bank transfer'
    ];
}
