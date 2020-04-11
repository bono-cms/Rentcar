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
use Rentcar\Service\FinderEntity;

final class Car extends AbstractController
{
    /**
     * Creates data for insert
     * 
     * @return array
     */
    private function createBookingData()
    {
        $finder = $this->createFinder();

        $data = $this->request->getPost('booking');
        $data = array_merge($data, $finder->getAsColumns());

        // Count amount
        $data['amount'] = $this->getModuleService('carService')->countAmount($data['car_id'], $finder->getPeriod());
        $data['currency'] = 'USD';

        return $data;
    }

    /**
     * Returns populated finder entity
     * 
     * @return \Rentcar\Service\FinderEntity
     */
    private function createFinder()
    {
        if ($this->request->hasPost('pickup', 'return')) {
            $this->sessionBag->set('rental', [
                'pickup' => $this->request->getPost('pickup'),
                'return' => $this->request->getPost('return')
            ]);
        }

        $data = $this->sessionBag->get('rental', []);

        return FinderEntity::factory($data);
    }

    /*
     * {@inheritDoc}
     */
    protected function bootstrap($action)
    {
        // Add global finder entity
        $this->view->addVariable('finder', $this->createFinder());

        parent::bootstrap($action);
    }

    /**
     * Book a car (Form submit processor)
     * 
     * @return mixed
     */
    public function bookAction()
    {
        $data = $this->createBookingData();

        $bookingService = $this->getModuleService('bookingService');

        // Double-check for availability
        $availability = $bookingService->carAvailability($data['car_id'], $data['checkin'], $data['checkout']);

        if ($availability['available'] === true) {
            $bookingService->createNew($data);
            $this->flashBag->set('success', 'You have successfully booked a car. Thank you for using our service!');
        } else {
            $this->flashBag->set('success', 'The car can not be reserved for provided dates');
        }

        return 1;
    }

    /**
     * List all cars
     * 
     * @param int $id
     * @return string
     */
    public function listAction($id)
    {
        $pageService = $this->getService('Pages', 'pageManager');
        $page = $pageService->fetchById($id, false);

        if ($page !== false) {
            // Load view plugins
            $this->loadSitePlugins();

            // Append breadcrumb
            $this->view->getBreadcrumbBag()
                       ->addOne($page->getName());

            return $this->view->render('car-list', array(
                'page' => $page,
                'languages' => $pageService->getSwitchUrls($id),
                'cars' => $this->getModuleService('carService')->fetchAll()
            ));

        } else {
            return false;
        }
    }

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
            // Append gallery
            $car->setGallery($this->getModuleService('carGalleryService')->fetchAll($id));
            
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
