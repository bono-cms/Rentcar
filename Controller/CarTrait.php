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

use Rentcar\Service\FinderEntity;
use Rentcar\Collection\PaymentMethodCollection;

trait CarTrait
{
    /**
     * Sends email notification to owner
     * 
     * @param array $transaction
     * @return boolean
     */
    protected function notifyOwner(array $transaction)
    {
        $payMethCol = new PaymentMethodCollection();

        // Prepare data. Client information.
        $client = [
            'Client name' => $transaction['name'],
            'Email' => $transaction['email'],
            'Phone' => $transaction['phone'],
            'Payment amount' => sprintf('%s %s', $transaction['amount'], $transaction['currency']),
            'Payment method' => $payMethCol->findByKey($transaction['method']),
            'Car' => $transaction['car'],
            'Pick up at' => sprintf('%s / %s', $transaction['pickup'], $transaction['checkin']), 
            'Return at' => sprintf('%s / %s', $transaction['return'], $transaction['checkout']),
            'Comment' => $transaction['comment']
        ];

        // Render message template
        $body = $this->view->renderRaw('Rentcar', 'admin', 'mail/notification', [
            'client' => $client
        ]);

        $subject = $this->translator->translate('You have received a new booking from %s', $transaction['name']);

        $mailer = $this->getService('Cms', 'mailer');
        return $mailer->send($subject, $body);
    }

    /**
     * Format error messages
     * 
     * @param string $error Error string
     * @param string $group Input group
     * @return string
     */
    protected function formatErrors($errors, $group)
    {
        $errors = json_decode($errors, false);
        $output = [];

        foreach ($errors as $name => $messages) {
            $output[sprintf('%s[%s]', $group, $name)] = $messages;
        }

        return json_encode($output);
    }

    /**
     * Checks whether car is available at the moment for booking
     * 
     * @param int $id Car id
     * @return boolean
     */
    protected function carAvailable($id)
    {
        $finder = $this->createFinder();
        $availability = $this->getModuleService('bookingService')->carAvailability($id, $finder->getCheckin(), $finder->getCheckout());

        return $availability !== false && $availability['available'] === true;
    }

    /**
     * Returns populated finder entity
     * 
     * @return \Rentcar\Service\FinderEntity
     */
    protected function createFinder()
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

    /**
     * Creates data for insert
     * 
     * @param array $request Request data
     * @param string $extension Payment extension
     * @param string $currency Payment currency
     * @return array|boolean Depending on success
     */
    protected function saveBooking(array $data, $extension, $currency)
    {
        $bookingService = $this->getModuleService('bookingService');
        $rentService = $this->getModuleService('rentService');

        $request = $this->parseRawData($data);

        // Append amount-related stuff
        $request['booking']['extension'] = $extension;
        $request['booking']['currency'] = $currency;

        if ($row = $bookingService->createNew($request['booking'])) {
            $rentService->saveBooking($bookingService->getLastId(), $request['service']['ids'], $request['period']);

            return $row;
        }

        // By default
        return false;
    }

    /**
     * Parse raw data we got from a form
     * 
     * @param array $request Request data
     * @return array|boolean Depending on success
     */
    protected function parseRawData(array $request)
    {
        // Services
        $rentService = $this->getModuleService('rentService');
        $carService = $this->getModuleService('carService');

        $finder = $this->createFinder();

        $serviceIds = isset($request['service']) ? array_keys($request['service']) : [];

        if ($serviceIds) {
            $servicePrice = $rentService->countAmount($serviceIds, $finder->getPeriod());
        } else {
            $servicePrice = 0;
        }

        $booking = $request['booking'];
        $booking = array_merge($booking, $finder->getAsColumns());

        // Count amount
        $booking['amount'] = $carService->countAmount($booking['car_id'], $finder->getPeriod());
        $booking['amount'] += $servicePrice;

        return [
            'service' => [
                'ids' => $serviceIds,
                'amount' => $servicePrice
            ],
            'booking' => $booking,
            'period' => $finder->getPeriod()
        ];
    }
}
