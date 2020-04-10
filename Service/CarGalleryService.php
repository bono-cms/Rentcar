<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Rentcar\Service;

use Krystal\Stdlib\VirtualEntity;
use Krystal\Image\Tool\ImageManagerInterface;
use Cms\Service\AbstractManager;
use Rentcar\Storage\CarGalleryMapperInterface;

final class CarGalleryService extends AbstractManager
{
    /**
     * Car gallery mapper
     * 
     * @var \Rentcar\Storage\CarGalleryMapperInterface
     */
    private $carGalleryMapper;

    /**
     * Image service
     * 
     * @var \Krystal\Image\Tool\ImageManagerInterface
     */
    private $imageService;

    /**
     * State initialization
     * 
     * @param \Rentcar\Storage\CarGalleryMapperInterface $carGalleryMapper
     * @param \Krystal\Image\Tool\ImageManagerInterface $imageService
     * @return void
     */
    public function __construct(CarGalleryMapperInterface $carGalleryMapper, ImageManagerInterface $imageService)
    {
        $this->carGalleryMapper = $carGalleryMapper;
        $this->imageService = $imageService;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $imageBag = clone $this->imageService->getImageBag();
        $imageBag->setId($row['id'])
                 ->setCover($row['image']);

        $entity = new VirtualEntity();
        $entity->setId($row['id'])
               ->setCarId($row['car_id'])
               ->setOrder($row['order'])
               ->setImage($row['image'])
               ->setImageBag($imageBag);

        return $entity;
    }

    /**
     * Returns last image id
     * 
     * @return int
     */
    public function getLastId()
    {
        return $this->carGalleryMapper->getMaxId();
    }

    /**
     * Fetch all images by associated car id
     * 
     * @param int $carId
     * @return array
     */
    public function fetchAll($carId)
    {
        return $this->prepareResults($this->carGalleryMapper->fetchAll($carId));
    }

    /**
     * Fetch single image by its id
     * 
     * @param int $id Image id
     * @return array
     */
    public function fetchById($id)
    {
        return $this->prepareResult($this->carGalleryMapper->findByPk($id));
    }

    /**
     * Delete single image by its id
     * 
     * @param int $id Image id
     * @return array
     */
    public function deleteById($id)
    {
        return $this->carGalleryMapper->deleteByPk($id) && $this->imageService->delete($id);
    }

    /**
     * Save an image
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function save(array $input)
    {
        // References
        $image =& $input['data']['image'];
        $files = isset($input['files']['image']) ? $input['files']['image'] : false;

        // Do we have some selected image?
        if ($files !== false) {
            // 1. Do we need to remove previous one?
            if (!empty($image['id']) && !empty($image['image'])) {
                $this->imageService->delete($image['id'], $image['image']);
            }

            // 2. Persist a row
            $image['image'] = $files['image']->getUniqueName();
            $this->carGalleryMapper->persist($image);

            // 3. Save image file
            $id = !empty($image['id']) ? $image['id'] : $this->getLastId();

            return $this->imageService->upload($id, $files);
        }

        return $this->carGalleryMapper->persist($image);
    }
}
