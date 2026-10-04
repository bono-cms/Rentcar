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

final class CarGallery extends AbstractController
{
    /**
     * Creates shared form
     * 
     * @param \Krystal\Stdlib\VirtualEntity $image
     * @param string $title Page title
     * @return string
     */
    private function createForm(VirtualEntity $image, $title)
    {
        $car = $this->getModuleService('carService')->fetchById($image->getCarId(), false);

        if ($car !== false) {
            // Append breadcrumbs
            $this->view->getBreadcrumbBag()
                       ->addOne('Cars', 'Rentcar:Admin:Car@indexAction')
                       ->addOne($this->translator->translate('Edit the car "%s"', $car->getName()), $this->createUrl('Rentcar:Admin:Car@editAction', [$image->getCarId()]))
                       ->addOne($title);

            return $this->view->render('car-gallery/form', [
                'image' => $image
            ]);

        } else {
            return false;
        }
    }

    /**
     * Renders new form
     * 
     * @param int $carId
     * @return string
     */
    public function addAction($carId)
    {
        $image = new VirtualEntity();
        $image->setCarId($carId);

        return $this->createForm($image, 'Upload new image');
    }

    /**
     * Renders edit form
     * 
     * @param int $id Image id
     * @return string
     */
    public function editAction($id)
    {
        $image = $this->getModuleService('carGalleryService')->fetchById($id);

        if ($image) {
            return $this->createForm($image, $this->translator->translate('Edit gallery image #%s', $id));
        } else {
            return false;
        }
    }

    /**
     * Deletes single image
     * 
     * @param int $id
     * @return mixed
     */
    public function deleteAction($id)
    {
        $this->getModuleService('carGalleryService')->deleteById($id);

        $this->flashBag->set('success', 'Gallery image has been removed successfully');

        return $this->json([
            'refresh' => true
        ]);
    }

    /**
     * Saves images
     * 
     * @return mixed
     */
    public function saveAction()
    {
        $input = $this->request->getAll();

        $validator = $this->createValidation();

        $validator->field('image.car_id')
                  ->required()
                  ->addRule('numeric');

        $validator->field('image.order')
                  ->addRule('numeric');

        $validator->file('file')
                  ->required('The image is required', empty($input['data']['image']['id']))
                  ->addRule('image');

        if ($validator->isPassed()) {
            $carGalleryService = $this->getModuleService('carGalleryService');
            $carGalleryService->save($input);

            if (!empty($input['data']['image']['id'])) {
                $this->flashBag->set('success', 'Gallery image has been updated successfully');

                return $this->json([
                    'refresh' => true
                ]);
            } else {
                $this->flashBag->set('success', 'Gallery image has been uploaded successfully');

                return $this->json([
                    'redirect' => $this->createUrl('Rentcar:Admin:CarGallery@editAction', [$carGalleryService->getLastId()]),
                ]);
            }

        } else {
            return $this->json([
                'errors' => $validator->getErrors()
            ]);
        }
    }
}