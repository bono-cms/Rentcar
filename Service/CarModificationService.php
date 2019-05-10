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

use Cms\Service\AbstractManager;
use Rentcar\Storage\CarModificationMapperInterface;

final class CarModificationService extends AbstractManager
{
    /**
     * Any compliant car modification mapper
     * 
     * @var \Rentcar\Storage\CarModificationMapperInterface
     */
    private $carModificationMapper;

    /**
     * State initialization
     * 
     * @param \Rentcar\Storage\CarModificationMapperInterface $carModificationMapper
     * @return void
     */
    public function __construct(CarModificationMapperInterface $carModificationMapper)
    {
        $this->carModificationMapper = $carModificationMapper;
    }
}
