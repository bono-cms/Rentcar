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

final class SiteService
{
    /**
     * Car service
     * 
     * @var \Rentcar\Service\CarService
     */
    private $carService;

    /**
     * State initialization
     * 
     * @param \Rentcar\Service\CarService $carService
     * @return void
     */
    public function __construct(CarService $carService)
    {
        $this->carService = $carService;
    }

    /**
     * Return all cars
     * 
     * @return array
     */
    public function getCars()
    {
        return $this->carService->fetchAll();
    }
}
