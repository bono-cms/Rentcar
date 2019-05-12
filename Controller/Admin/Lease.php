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
        $leaseService = $this->getModuleService('leaseService');

        $contracts = $this->getFilter($leaseService, $this->createUrl('Rentcar:Admin:Lease@indexAction', array(null)));

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Cars', 'Rentcar:Admin:Car@indexAction')
                                       ->addOne('Lease');

        return $this->view->render('lease/index', array(
            'contracts' => $contracts,
            'models' => $leaseService->fetchModels(),
            'paginator' => $leaseService->getPaginator()
        ));
    }

    /**
     * Renders lease item
     * 
     * @param int $id
     * @return mixed
     */
    public function viewAction($id)
    {
        $lease = $this->getModuleService('leaseService')->fetchById($id);

        if ($lease !== false) {
            // Append breadcrumbs
            $this->view->getBreadcrumbBag()->addOne('Cars', 'Rentcar:Admin:Car@indexAction')
                                           ->addOne('Lease', 'Rentcar:Admin:Lease@indexAction')
                                           ->addOne('View contract');
            
            return $this->view->render('lease/view', array(
                'lease' => $lease
            ));
        } else {
            return false;
        }
    }

    /**
     * Renders lease form
     * 
     * @param \Krystal\Stdlib\VirtualEntity $lease
     * @return string
     */
    private function createForm(VirtualEntity $lease)
    {
        // Load datepicker plugin
        $this->view->getPluginBag()->load('datepicker');

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Cars', 'Rentcar:Admin:Car@indexAction')
                                       ->addOne('Lease', 'Rentcar:Admin:Lease@indexAction')
                                       ->addOne($lease->getId() ? 'Edit the lease contract' : 'Add new lease contract');

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
        // Grab lease service
        $leaseService = $this->getModuleService('leaseService');

        // Batch removal
        if ($this->request->hasPost('batch')) {
            $ids = array_keys($this->request->getPost('batch'));

            $leaseService->deleteByIds($ids);
            $this->flashBag->set('success', 'Selected elements have been removed successfully');

        } else {
            $this->flashBag->set('warning', 'You should select at least one element to remove');
        }

        // Single removal
        if (!empty($id)){
            $leaseService->deleteById($id);
            $this->flashBag->set('success', 'Selected element has been removed successfully');
        }

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
            $this->flashBag->set('success', 'The element has been updated successfully');
            return 1;
        } else {
            $this->flashBag->set('success', 'The element has been created successfully');
            return $leaseService->getLastId();
        }
    }
}
