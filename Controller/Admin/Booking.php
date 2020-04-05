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

        return $this->view->render('booking/index', [
            'bookings' => $this->getModuleService('bookingService')->fetchAll()
        ]);
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
}
