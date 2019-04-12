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
use Rentcar\Collection\LeasePeriodCollection;
use Rentcar\Collection\StatusCollection;

final class Lease extends AbstractController
{
    /**
     * Renders lease items
     * 
     * @return string
     */
    public function indexAction()
    {
        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Cars', 'Rentcar:Admin:Car@indexAction')
                                       ->addOne('Lease');

        return $this->view->render('lease/index', array(
            'contracts' => $this->getModuleService('leaseService')->fetchAll()
        ));
    }

    /**
     * Renders lease form
     * 
     * @param \Krystal\Stdlib\VirtualEntity $lease
     * @return string
     */
    private function createForm(VirtualEntity $lease)
    {
        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Cars', 'Rentcar:Admin:Car@indexAction')
                                       ->addOne('Lease', 'Rentcar:Admin:Lease@indexAction')
                                       ->addOne(is_array($lease) ? 'Edit the lease contract' : 'Add new lease contract');

        $lpCol = new LeasePeriodCollection();
        $stCol = new StatusCollection();

        return $this->view->render('lease/form', array(
            'lease' => $lease,
            'periods' => $lpCol->getAll(),
            'statuses' => $stCol->getAll()
        ));
    }

    /**
     * Renders add form
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
     * @param int $id Lease id
     * @return mixed
     */
    public function editAction($id)
    {
        $lease = $this->getModuleService('leaseService')->fetchById($id);

        if ($lease !== false) {
            return $this->createForm($lease);
        } else {
            return false;
        }
    }

    /**
     * Deletes a lease by its id
     * 
     * @param int $id Lease id
     * @return mixed
     */
    public function deleteAction($id)
    {
        $this->getModuleService('leaseService')->deleteById($id);
        return 1;
    }

    /**
     * Saves a lease
     * 
     * @return mixed
     */
    public function saveAction()
    {
        $input = $this->request->getPost('lease');

        $leaseService = $this->getModuleService('leaseService');
        $leaseService->save($input);

        if ($input['id']) {
            return 1;
        } else {
            return $leaseService->getLastId();
        }
    }
}
