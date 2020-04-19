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
use Payment\Collection\ResponseCodeCollection;
use Payment\Controller\PaymentTrait;
use Rentcar\Collection\PaymentMethodCollection;
use Krystal\Stdlib\VirtualEntity;

final class Car extends AbstractController
{
    use PaymentTrait;
    use CarTrait;

    /*
     * {@inheritDoc}
     */
    protected function bootstrap($action)
    {
        // Disabled CSRF for gateway action
        if ($action === 'responseAction') {
            $this->enableCsrf = false;
        }

        parent::bootstrap($action);

        // Add global finder entity
        $this->view->addVariable('finder', $this->createFinder());
    }

    /**
     * Handle success or failure after payment gets done
     * 
     * @param string $token Unique transaction token
     * @return mixed
     */
    public function responseAction($token)
    {
        // Find transaction row by its token
        $transaction = $this->getModuleService('bookingService')->fetchByToken($token);
        $response = $this->createResponse($transaction['extension']);

        if ($response->canceled()) {
            return $this->renderResponse(ResponseCodeCollection::RESPONSE_CANCEL);
        } else {
            // Now confirm payment by token, since its successful
            $this->getModuleService('bookingService')->confirmPayment($token);
            return $this->renderResponse(ResponseCodeCollection::RESPONSE_SUCCESS);
        }
    }

    /**
     * Book a car (Form submit processor)
     * 
     * @return mixed
     */
    public function bookAction()
    {
        // Whether payment needs to be done via card?
        $isCard = $this->request->getPost('method') == PaymentMethodCollection::METHOD_CARD;

        $transaction = $this->saveBooking($this->request->getPost(), $isCard ? 'Prime4G' : '', 'USD');

        // Is this by card?
        if (is_array($transaction) && $isCard) {
            return $this->renderGateway('Rentcar:Car@responseAction', $transaction);
        }

        $title = $transaction ? 'You have booked a car' : 'An error occurred';

        $page = new VirtualEntity();
        $page->setTitle($this->translator->translate($title));

        // Load site plugins
        $this->loadSitePlugins();

        return $this->view->render('car-booked', [
            'page' => $page,
            'languages' => $this->getService('Cms', 'languageManager')->fetchAll(true),
            'success' => $transaction !== false
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
                'languages' => $pageService->getSwitchUrls($id, 'Rentcar:Car@listAction'),
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
                'languages' => $carService->getSwitchUrls($id),
                'available' => $this->carAvailable($id)
            ));

        } else {
            // Wrong ID provided. Trigger 404 Error
            return false;
        }
    }
}
