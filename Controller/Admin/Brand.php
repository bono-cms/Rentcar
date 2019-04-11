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
     * @param mixed $brand
     * @return string
     */
    private function createForm($brand)
    {
        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Cars', 'Rentcar:Admin:Car@indexAction')
                                       ->addOne('Brands', 'Rentcar:Admin:Brand@indexAction')
                                       ->addOne(is_array($brand) ? 'Edit the brand' : 'Add new brand');

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
        $input = $this->request->getPost('brand');

        $brandService = $this->getModuleService('brandService');
        $brandService->save($input);

        if ($input['id']) {
            return 1;
        } else {
            return $brandService->getLastId();
        }
    }
}
