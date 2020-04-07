<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Rentcar\Controller\Admin;

use Cms\Controller\Admin\AbstractController;
use Krystal\Stdlib\VirtualEntity;
use Rentcar\Collection\OrderStatusCollection;
use Rentcar\Collection\GenderCollection;

final class Booking extends AbstractController
{
    /**
     * Render all booking entries
     * 
     * @return string
     */
    public function indexAction()
    {
        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Cars', 'Rentcar:Admin:Car@indexAction')
                                       ->addOne('Bookings');

        $orderStCol = new OrderStatusCollection();
        $bookingService = $this->getModuleService('bookingService');

        return $this->view->render('booking/index', [
            'bookings' => $this->getFilter($bookingService),

            // Vars for filters
            'cars' => $this->getModuleService('carService')->fetchList(),
            'orderStatuses' => $orderStCol->getAll(),
            'filterApplied' => $this->request->getQuery('filter', false)
        ]);
    }

    /**
     * Create booking form
     * 
     * @param \Krystal\Stdlib\VirtualEntity $booking
     * @param string $title Page title
     * @return string
     */
    private function createForm(VirtualEntity $booking, $title)
    {
        $orderStCol = new OrderStatusCollection();
        $genderStCol = new GenderCollection();

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Cars', 'Rentcar:Admin:Car@indexAction')
                                       ->addOne('Bookings', 'Rentcar:Admin:Booking@indexAction')
                                       ->addOne($title);

        return $this->view->render('booking/form', [
            'booking' => $booking,
            'orderStatuses' => $orderStCol->getAll(),
            'cars' => $this->getModuleService('carService')->fetchList(),
            'genders' => $genderStCol->getAll()
        ]);
    }

    /**
     * Renders adding form
     * 
     * @return string
     */
    public function addAction()
    {
        return $this->createForm(new VirtualEntity, 'Add new booking entry');
    }

    /**
     * Renders edit form
     * 
     * @param int $id Booking id
     * @return string
     */
    public function editAction($id)
    {
        $booking = $this->getModuleService('bookingService')->fetchById($id);

        if ($booking) {
            return $this->createForm($booking, sprintf('Edit booking entry from "%s"', $booking->getName()));
        } else {
            return false;
        }
    }

    /**
     * Render booking details by its id
     * 
     * @param int $id Booking Id
     * @return string
     */
    public function detailsAction($id)
    {
        $booking = $this->getModuleService('bookingService')->fetchById($id);

        if ($booking !== false) {
            // Append breadcrumbs
            $this->view->getBreadcrumbBag()->addOne('Cars', 'Rentcar:Admin:Car@indexAction')
                                           ->addOne('Bookings', 'Rentcar:Admin:Booking@indexAction')
                                           ->addOne(sprintf('View booking details #%s', $id));

            return $this->view->render('booking/details', [
                'booking' => $booking
            ]);
        } else {
            return false;
        }
    }

    /**
     * Deletes booking entry
     * 
     * @param int $id Booking Id
     * @return mixed
     */
    public function deleteAction($id)
    {
        if ($this->getModuleService('bookingService')->deleteById($id)) {
            $this->flashBag->set('success', 'Selected booking entry has been deleted');
        }

        return 1;
    }

    /**
     * Save booking form
     * 
     * @return mixed
     */
    public function saveAction()
    {
        // Raw input data
        $input = $this->request->getPost('booking');

        $bookingService = $this->getModuleService('bookingService');
        $bookingService->save($input);

        if ($input['id']) {
            $this->flashBag->set('success', 'Booking entry has been updated successfully');
            return 1;
        } else {
            $this->flashBag->set('success', 'Booking entry has created successfully');
            return $bookingService->getLastId();
        }
    }
}
