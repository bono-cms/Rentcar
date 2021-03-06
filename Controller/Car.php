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
     * Counts price
     * 
     * @return string
     */
    public function countAction()
    {
        $data = $this->parseRawData($this->request->getPost());
        $amount = $data['booking']['amount'];

        return $this->json([
            'amount' => $amount,
            'price' => number_format($amount)
        ]);
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
            // Payment is successful, so now just send email notification
            $this->notifyOwner($transaction);

            // Now confirm payment by token, since its successful
            $this->getModuleService('bookingService')->confirmPayment($token);
            return $this->renderResponse(ResponseCodeCollection::RESPONSE_SUCCESS);
        }
    }

    /**
     * Renders gateway page
     * 
     * @param string $token Transaction token
     * @return string
     */
    public function gatewayAction($token)
    {
        $transaction = $this->getModuleService('bookingService')->fetchByToken($token);

        if ($transaction) {
            return $this->renderGateway('Rentcar:Car@responseAction', $transaction);
        } else {
            return false;
        }
    }

    /**
     * Renders finish page
     * 
     * @param string $token Transaction token
     * @return string
     */
    public function finishAction($token)
    {
        $transaction = $this->getModuleService('bookingService')->fetchByToken($token);

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
     * Book a car (Form submit processor)
     * 
     * @return mixed
     */
    public function bookAction()
    {
        $request = $this->request->getPost();

        $formValidator = $this->createValidator([
            'input' => [
                'source' => $request['booking'],
                'definition' => [
                    'name' => new Pattern\Name,
                    'email' => new Pattern\Email,
                    'phone' => new Pattern\Phone
                ]
            ]
        ]);

        // Validate booking form, before processing
        if ($formValidator->isValid()) {
            // Whether payment needs to be done via card?
            $isCard = $request['booking']['method'] == PaymentMethodCollection::METHOD_CARD;

            $transaction = $this->saveBooking($request, $isCard ? 'Prime4G' : '', 'USD', $isCard);

            // Is this by card?
            if (is_array($transaction) && $isCard) {
                return $this->json([
                    'backUrl' => $this->createUrl('Rentcar:Car@gatewayAction', [$transaction['token']])
                ]);
            }

            // Send email notification
            $this->notifyOwner($transaction);

            return $this->json([
                'backUrl' => $this->createUrl('Rentcar:Car@finishAction', [$transaction['token']])
            ]);

        } else {
            return $this->formatErrors($formValidator->getErrors(), 'booking');
        }
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
