<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Rentcar;

use Krystal\Image\Tool\ImageManager;
use Cms\AbstractCmsModule;
use Rentcar\Service\CarService;
use Rentcar\Service\CarGalleryService;
use Rentcar\Service\BrandService;
use Rentcar\Service\LeaseService;
use Rentcar\Service\SiteService;
use Rentcar\Service\CarModificationService;
use Rentcar\Service\RentService;
use Rentcar\Service\BookingService;

final class Module extends AbstractCmsModule
{
    /* Constants */
    const IMG_PATH_CARS = '/data/uploads/module/rent-car';
    const IMG_PATH_BRAND = '/data/uploads/module/rent-car/brands';

    /**
     * Returns album image manager
     * 
     * @return \Krystal\Image\Tool\ImageManager
     */
    private function createImageService()
    {
        $plugins = array(
            'thumb' => array(
                'dimensions' => array(
                    // Administration area
                    array(350, 350)
                )
            ),

            'original' => array(
                'prefix' => 'original'
            )
        );

        return new ImageManager(
            self::IMG_PATH_CARS,
            $this->appConfig->getRootDir(),
            $this->appConfig->getRootUrl(),
            $plugins
        );
    }

    /**
     * Returns brand icon service
     * 
     * @return \Krystal\Image\Tool\ImageManager
     */
    private function createBrandIcon()
    {
        $plugins = array(
            'original' => array(
                'prefix' => 'original'
            )
        );

        return new ImageManager(
            self::IMG_PATH_BRAND,
            $this->appConfig->getRootDir(),
            $this->appConfig->getRootUrl(),
            $plugins
        );
    }

    /**
     * {@inhertiDoc}
     */
    public function getServiceProviders()
    {
        $imageService = $this->createImageService();

        $carService = new CarService($this->getMapper('\Rentcar\Storage\MySQL\CarMapper'), $this->getWebPageManager(), $imageService);
        $brandService = new BrandService($this->getMapper('\Rentcar\Storage\MySQL\BrandMapper'), $this->createBrandIcon());
        $carModificationService = new CarModificationService($this->getMapper('\Rentcar\Storage\MySQL\CarModificationMapper'));

        return array(
            'carService' => $carService,
            'carGalleryService' => new CarGalleryService($this->getMapper('\Rentcar\Storage\MySQL\CarGalleryMapper')),
            'carModificationService' => $carModificationService,
            'siteService' => new SiteService($carService, $brandService, $carModificationService),
            'brandService' => $brandService,
            'leaseService' => new LeaseService($this->getMapper('\Rentcar\Storage\MySQL\LeaseMapper')),
            'rentService' => new RentService($this->getMapper('\Rentcar\Storage\MySQL\RentServiceMapper')),
            'bookingService' => new BookingService($this->getMapper('\Rentcar\Storage\MySQL\BookingMapper'))
        );
    }
}
