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

use Cms\AbstractCmsModule;
use Rentcar\Service\CarService;
use Rentcar\Service\BrandService;
use Rentcar\Service\LeaseService;

final class Module extends AbstractCmsModule
{
    /**
     * {@inhertiDoc}
     */
    public function getServiceProviders()
    {
        return array(
            'carService' => new CarService($this->getMapper('\Rentcar\Storage\MySQL\CarMapper'), $this->getWebPageManager()),
            'brandService' => new BrandService($this->getMapper('\Rentcar\Storage\MySQL\BrandMapper')),
            'leaseService' => new LeaseService($this->getMapper('\Rentcar\Storage\MySQL\LeaseMapper'))
        );
    }
}
