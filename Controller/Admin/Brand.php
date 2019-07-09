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

final class Brand extends AbstractController
{
    /**
     * Renders shared form
     * 
     * @param \Krystal\Stdlib\VirtualEntity $brand
     * @return string
     */
    private function createForm(VirtualEntity $brand)
    {
        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Cars', 'Rentcar:Admin:Car@indexAction')
                                       ->addOne('Brands', 'Rentcar:Admin:Brand@indexAction')
                                       ->addOne($brand->getId() ? 'Edit the brand' : 'Add new brand');

        return $this->view->render('brand/form', array(
            'brand' => $brand
        ));
    }

    /**
     * Render all brands
     * 
     * @return string
     */
    public function indexAction()
    {
        // Append breadcrumb
        $this->view->getBreadcrumbBag()->addOne('Cars', 'Rentcar:Admin:Car@indexAction')
                                       ->addOne('Brands');

        return $this->view->render('brand/index', array(
            'brands' => $this->getModuleService('brandService')->fetchAll()
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
     * @param int $id
     * @return string
     */
    public function editAction($id)
    {
        $brand = $this->getModuleService('brandService')->fetchById($id);

        if ($brand !== false) {
            return $this->createForm($brand);
        } else {
            return false;
        }
    }

    /**
     * Deletes a brand by its id
     * 
     * @param int $id
     * @return mixed
     */
    public function deleteAction($id)
    {
        $this->getModuleService('brandService')->deleteById($id);

        $this->flashBag->set('success', 'Selected element has been removed successfully');
        return 1;
    }

    /**
     * Saves a brand
     * 
     * @return string
     */
    public function saveAction()
    {
        // Get raw POST data
        $input = $this->request->getAll();

        $brandService = $this->getModuleService('brandService');
        $brandService->save($input);

        if ($input['data']['brand']['id']) {

            $this->flashBag->set('success', 'The element has been updated successfully');
            return 1;
        } else {

            $this->flashBag->set('success', 'The element has been created successfully');
            return $brandService->getLastId();
        }
    }
}
