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

use Cms\Service\AbstractSiteBootstrapper;

final class SiteBootstrapper extends AbstractSiteBootstrapper
{
    /**
     * {@inheritDoc}
     */
    public function bootstrap()
    {
        $rentCar = $this->moduleManager->getModule('Rentcar');

        $siteService = $rentCar->getService('siteService');
        $bookingService = $rentCar->getService('bookingService');

        $this->view->addVariables([
            'carService' => $siteService,
            'availableCarCount' => $bookingService = $bookingService->getAvailabilityCount()
        ]);
    }
}
