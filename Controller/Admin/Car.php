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

final class Car extends AbstractController
{
    /**
     * Render all cars
     * 
     * @return string
     */
    public function indexAction()
    {
        // Append a breadcrumb
        $this->view->getBreadcrumbBag()
                   ->addOne('Cars');

        return $this->view->render('car/index', array(
            'cars' => $this->getModuleService('carService')->fetchAll()
        ));
    }

    /**
     * Renders car form
     * 
     * @param mixed $car
     * @return string
     */
    private function createForm($car)
    {
        // Load view plugins
        $this->view->getPluginBag()->load($this->getWysiwygPluginName());

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()
                   ->addOne('Cars', 'Rentcar:Admin:Car@indexAction')
                   ->addOne(is_array($car) ? 'Edit the car' : 'Add new car');

        return $this->view->render('car/form', array(
            'car' => $car,
            'brands' => $this->getModuleService('brandService')->fetchList(),
            'modifications' => is_array($car) ? $this->getModuleService('carModificationService')->fetchAll($car[0]->getId()) : array()
        ));
    }

    /**
     * Renders edit form by 
     * 
     * @param int $id Car id
     * @return string
     */
    public function editAction($id)
    {
        $car = $this->getModuleService('carService')->fetchById($id, true);

        if ($car !== false) {
            return $this->createForm($car);
        } else {
            return false;
        }
    }

    /**
     * Renders adding form
     * 
     * @return string
     */
    public function addAction()
    {
        return $this->createForm(new VirtualEntity);
    }

    /**
     * Saves a car
     * 
     * @return mixed
     */
    public function saveAction()
    {
        $input = $this->request->getAll();

        $carService = $this->getModuleService('carService');
        $carService->save($input);

        if ($input['data']['car']['id']) {
            $this->flashBag->set('success', 'The element has been updated successfully');
            return 1;
        } else {
            $this->flashBag->set('success', 'The element has been created successfully');
            return $carService->getLastId();
        }
    }

    /**
     * Deletes a car by its id
     * 
     * @param int $id Car id
     * @return int
     */
    public function deleteAction($id)
    {
        $service = $this->getModuleService('carService');

        // Batch removal
        if ($this->request->hasPost('batch')) {
            $ids = array_keys($this->request->getPost('batch'));

            $service->deleteByIds($ids);
            $this->flashBag->set('success', 'Selected elements have been removed successfully');

        } else {
            $this->flashBag->set('warning', 'You should select at least one element to remove');
        }

        // Single removal
        if (!empty($id)) {
            $service->deleteById($id);
            $this->flashBag->set('success', 'Selected element has been removed successfully');
        }

        return 1;
    }
}
