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

use Rentcar\Storage\CarMapperInterface;
use Cms\Service\AbstractManager;

final class CarService extends AbstractManager
{
    /**
     * Any compliant car mapper
     * 
     * @var \Rentcar\Storage\CarMapperInterface
     */
    private $carMapper;

    /**
     * State initialization
     * 
     * @param \Rentcar\Storage\CarMapperInterface $carMapper
     * @return void
     */
    public function __construct(CarMapperInterface $carMapper)
    {
        $this->carMapper = $carMapper;
    }
}
