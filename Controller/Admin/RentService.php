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

final class RentService extends AbstractController
{
    /**
     * Render all services
     * 
     * @return string
     */
    public function indexAction()
    {
        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Cars', 'Rentcar:Admin:Car@indexAction')
                                       ->addOne('Rent services');

        return $this->view->render('rent-service/index', [
            'services' => $this->getModuleService('rentService')->fetchAll(false)
        ]);
    }

    /**
     * Creates a form
     * 
     * @param array|\Krystal\Stdlib\VirtualEntity $service
     * @return string
     */
    private function createForm($service)
    {
        // Load view plugins
        $this->view->getPluginBag()->load($this->getWysiwygPluginName());

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Cars', 'Rentcar:Admin:Car@indexAction')
                                       ->addOne('Rent services', 'Rentcar:Admin:RentService@indexAction')
                                       ->addOne(is_object($service) ? 'Add new rent service' : 'Update rent service');

        return $this->view->render('rent-service/form', [
            'service' => $service
        ]);
    }

    /**
     * Renders a form
     * 
     * @return string
     */
    public function addAction()
    {
        return $this->createForm(new VirtualEntity);
    }

    /**
     * Renders edit form
     * 
     * @param int $id Service id
     * @return string
     */
    public function editAction($id)
    {
        $service = $this->getModuleService('rentService')->fetchById($id, true);

        if ($service !== false) {
            return $this->createForm($service);
        } else {
            return false;
        }
    }

    /**
     * Deletes a service
     * 
     * @param int $id Service id
     * @return mixed
     */
    public function deleteAction($id)
    {
        if ($this->getModuleService('rentService')->deleteById($id)) {
            $this->flashBag->set('success', 'A service has been deleted successfully');
        }

        return 1;
    }

    /**
     * Saves a service
     * 
     * @return mixed
     */
    public function saveAction()
    {
        $input = $this->request->getPost('service');

        $rentService = $this->getModuleService('rentService');
        $rentService->save($input);

        if (!empty($input)) {
            $this->flashBag->set('success', 'A service has been updated successfully');
            return 1;
        } else {
            $this->flashBag->set('success', 'A service has been created successfully');
            return $rentService->getLastId();
        }
    }
}
