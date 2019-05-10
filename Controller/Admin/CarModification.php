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
     * @return string
     */
    private function createForm($modification)
    {
        $carId = is_array($modification) ? $modification[0]->getCarId() : $modification->getCarId();

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()
                   ->addOne('Cars', 'Rentcar:Admin:Car@indexAction')
                   ->addOne('Edit the car', $this->createUrl('Rentcar:Admin:Car@editAction', array($carId)))
                   ->addOne(is_array($modification) ? 'Update car modification' : 'Add new car modification');

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

        return $this->createForm($modification);
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
            return $this->createForm($modification);
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
            return 1;
        } else {
            return $carModificationService->getLastId();
        }
    }
}
