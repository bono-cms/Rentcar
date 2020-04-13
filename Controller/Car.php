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
use Krystal\Stdlib\VirtualEntity;

final class Car extends AbstractController
{
    /**
     * Creates data for insert
     * 
     * @param array $request Request data
     * @return boolean
     */
    private function saveBooking(array $request)
    {
        // Services
        $bookingService = $this->getModuleService('bookingService');
        $rentService = $this->getModuleService('rentService');
        $carService = $this->getModuleService('carService');

        $finder = $this->createFinder();

        $serviceIds = array_keys(isset($request['service']) ? $request['service'] : []);

        $booking = $request['booking'];
        $booking = array_merge($booking, $finder->getAsColumns());

        // Count amount
        $booking['amount'] = $carService->countAmount($booking['car_id'], $finder->getPeriod());
        $booking['amount'] += $rentService->countAmount($serviceIds, $finder->getPeriod());
        $booking['currency'] = 'USD';

        if ($bookingService->createNew($booking)) {
            return $rentService->saveBooking($bookingService->getLastId(), $serviceIds, $finder->getPeriod());
        }

        // By default
        return false;
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
        $success = $this->saveBooking($this->request->getPost());

        $title = $success ? 'You have booked a car' : 'An error occurred';

        $page = new VirtualEntity();
        $page->setTitle($this->translator->translate($title));

        // Load site plugins
        $this->loadSitePlugins();

        return $this->view->render('car-booked', [
            'page' => $page,
            'languages' => $this->getService('Cms', 'languageManager')->fetchAll(true),
            'success' => $success
        ]);
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
            // Append services
            $this->getModuleService('rentService')->appendServices($car);

            // Load view plugins
            $this->loadSitePlugins();

            // Append breadcrumb
            $this->view->getBreadcrumbBag()
                       ->addOne($car->getName());

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
