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
     * Car modification service
     * 
     * @var \Rentcar\Service\CarModificationService
     */
    private $carModificationService;

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
     * @param \Rentcar\Service\CarModificationService $carModificationService
     * @return void
     */
    public function __construct(CarService $carService, BrandService $brandService, CarModificationService $carModificationService)
    {
        $this->carService = $carService;
        $this->brandService = $brandService;
        $this->carModificationService = $carModificationService;
    }

    /**
     * Get global prices
     * 
     * @param mixed $carId Optional car ID constraint
     * @return array
     */
    public function getPrices($carId = null)
    {
        return $this->carModificationService->getPrices($carId);
    }

    /**
     * Return all brands
     * 
     * @return array
     */
    public function getBrands()
    {
        return $this->brandService->fetchAll(true);
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
