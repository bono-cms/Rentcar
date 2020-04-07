<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Rentcar\Storage;

interface BookingMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public function filter($input, $page, $itemsPerPage, $sortingColumn, $desc, array $parameters = []);
}
