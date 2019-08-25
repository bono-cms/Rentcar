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

final class CarModification extends AbstractController
{
    /**
     * Creates a form
     * 
     * @param mixed $entity
     * @param string $title Page title
     * @return string
     */
    private function createForm($modification, $title)
    {
        $carId = is_array($modification) ? $modification[0]->getCarId() : $modification->getCarId();
        $car = $this->getModuleService('carService')->fetchById($carId, false);

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()
                   ->addOne('Cars', 'Rentcar:Admin:Car@indexAction')
                   ->addOne($this->translator->translate('Edit the car "%s"', $car->getName()), $this->createUrl('Rentcar:Admin:Car@editAction', array($carId)))
                   ->addOne($title);

        return $this->view->render('car-modification/form', array(
            'modification' => $modification
        ));
    }

    /**
     * Renders add form
     * 
     * @param int $carId
     * @return string
     */
    public function addAction($carId)
    {
        $modification = new VirtualEntity;
        $modification->setCarId($carId);

        return $this->createForm($modification, 'Add new car modification');
    }

    /**
     * Renders edit form
     * 
     * @param int $id Car modification id
     * @return string
     */
    public function editAction($id)
    {
        $modification = $this->getModuleService('carModificationService')->fetchById($id, true);

        if ($modification !== false) {
            $name = $this->getCurrentProperty($modification, 'name');
            return $this->createForm($modification, $this->translator->translate('Update car modification "%s"', $name));
        } else {
            return false;
        }
    }

    /**
     * Deletes car modification by its id
     * 
     * @param int $id Car modification id
     * @return string
     */
    public function deleteAction($id)
    {
        $this->getModuleService('carModificationService')->deleteById($id);

        $this->flashBag->set('success', 'Selected element has been removed successfully');
        return 1;
    }

    /**
     * Saves car modification
     * 
     * @return string
     */
    public function saveAction()
    {
        // Get raw POST data
        $input = $this->request->getPost();

        $carModificationService = $this->getModuleService('carModificationService');
        $carModificationService->save($input);

        if ($input['modification']['id']) {
            $this->flashBag->set('success', 'The element has been updated successfully');
            return 1;
        } else {
            $this->flashBag->set('success', 'The element has been created successfully');
            return $carModificationService->getLastId();
        }
    }
}
