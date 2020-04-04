<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Rentcar\Controller;

use Site\Controller\AbstractController;

final class Car extends AbstractController
{
    /**
     * Renders car by its id
     * 
     * @param int $id Car id
     * @return string
     */
    public function carAction($id)
    {
        $carService = $this->getModuleService('carService');
        $car = $carService->fetchById($id, false);

        if ($car !== false) {
            // Load view plugins
            $this->loadSitePlugins();

            // Append breadcrumb
            $this->view->getBreadcrumbBag()
                       ->addOne($car->getName());

            // Append services
            $this->getModuleService('rentService')->appendServices($car);

            return $this->view->render('car-single', array(
                'page' => $car,
                'car' => $car,
                'languages' => $carService->getSwitchUrls($id)
            ));

        } else {
            // Wrong ID provided. Trigger 404 Error
            return false;
        }
    }
}
