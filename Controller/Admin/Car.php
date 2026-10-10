<?php

/**
 * This file is part of the Bono CMS
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

        return $this->view->render('car/index', [
            'cars' => $this->getModuleService('carService')->fetchAll(),
            'newBookings' => $this->getModuleService('bookingService')->countNew()
        ]);
    }

    /**
     * Renders car form
     * 
     * @param mixed $car
     * @param string $title Page title
     * @return string
     */
    private function createForm($car, $title)
    {
        // Load view plugins
        $this->view->getPluginBag()->load([$this->getWysiwygPluginName(), 'chosen']);

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()
                   ->addOne('Cars', 'Rentcar:Admin:Car@indexAction')
                   ->addOne($title);

        return $this->view->render('car/form', [
            'car' => $car,
            'brands' => $this->getModuleService('brandService')->fetchList(),
            'services' => $this->getModuleService('rentService')->fetchList(),
            'activeServiceIds' => is_array($car) ? $this->getModuleService('rentService')->fetchAttachedIds($car[0]->getId()) : [],
            'modifications' => is_array($car) ? $this->getModuleService('carModificationService')->fetchAll($car[0]->getId()) : [],
            'gallery' => is_array($car) ? $this->getModuleService('carGalleryService')->fetchAll($car[0]->getId()) : []
        ]);
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
            $name = $this->getCurrentProperty($car, 'name');
            return $this->createForm($car, $this->translator->translate('Edit the car "%s"', $name));
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
        // CMS configuration object
        $config = $this->getService('Cms', 'configManager')->getEntity();

        $car = new VirtualEntity;
        $car->setChangeFreq($config->getSitemapFrequency())
            ->setPriority($config->getSitemapPriority());

        return $this->createForm($car, 'Add new car');
    }

    /**
     * Saves a car
     * 
     * @return mixed
     */
    public function saveAction()
    {
        $input = $this->request->getAll();

        $validator = $this->createValidation();

        $validator->field('car.price')
                  ->required()
                  ->addRule('numeric');

        $validator->field('car.order')
                  ->addRule('numeric');

        $validator->field('car.qty')
                  ->addRule('numeric');

        $validator->field('car.rent')
                  ->addRule('numeric');

        $validator->field('translation.*.name')
                  ->required();

        if ($validator->isPassed()) {
            $carService = $this->getModuleService('carService');
            $carService->save($input);

            if ($input['data']['car']['id']) {
                $this->flashBag->set('success', 'The element has been updated successfully');

                return $this->json([
                    'refresh' => true
                ]);
            } else {
                $this->flashBag->set('success', 'The element has been created successfully');

                return $this->json([
                    'redirect' => $this->createUrl('Rentcar:Admin:Car@editAction', [$carService->getLastId()]),
                ]);
            }

        } else {
            return $this->json([
                'errors' => $validator->getErrors()
            ]);
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

        return $this->json([
            'refresh' => true
        ]);
    }
}