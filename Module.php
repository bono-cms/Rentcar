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
use Rentcar\Service\BrandService;
use Rentcar\Service\LeaseService;
use Rentcar\Service\SiteService;
use Rentcar\Service\CarModificationService;

final class Module extends AbstractCmsModule
{
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
            '/data/uploads/module/rent-car',
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
            '/data/uploads/module/rent-car/brands',
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

        return array(
            'carService' => $carService,
            'carModificationService' => new CarModificationService($this->getMapper('\Rentcar\Storage\MySQL\CarModificationMapper')),
            'siteService' => new SiteService($carService, $brandService),
            'brandService' => $brandService,
            'leaseService' => new LeaseService($this->getMapper('\Rentcar\Storage\MySQL\LeaseMapper')),
        );
    }
}
