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
     * Brand service instance
     * 
     * @var \Rentcar\Service\BrandService
     */
    private $brandService;

    /**
     * State initialization
     * 
     * @param \Rentcar\Service\CarService $carService
     * @param \Rentcar\Service\BrandService $brandService
     * @return void
     */
    public function __construct(CarService $carService, BrandService $brandService)
    {
        $this->carService = $carService;
        $this->brandService = $brandService;
    }

    /**
     * Return all brands
     * 
     * @return array
     */
    public function getBrands()
    {
        return $this->brandService->fetchAll();
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
