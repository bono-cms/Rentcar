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
     * State initialization
     * 
     * @param \Rentcar\Storage\CarGalleryMapperInterface $carGalleryMapper
     * @return void
     */
    public function __construct(CarGalleryMapperInterface $carGalleryMapper)
    {
        $this->carGalleryMapper = $carGalleryMapper;
    }
    
    /**
     * Fetch all images by associated car id
     * 
     * @param int $carId
     * @return array
     */
    public function fetchAll($carId)
    {
        return $this->carGalleryMapper->fetchAll($carId);
    }

    /**
     * Fetch single image by its id
     * 
     * @param int $id Image id
     * @return array
     */
    public function fetchById($id)
    {
        return $this->carGalleryMapper->findByPk($id);
    }

    /**
     * Fetch single image by its id
     * 
     * @param int $id Image id
     * @return array
     */
    public function deleteById($id)
    {
        return $this->carGalleryMapper->deleteByPk($id);
    }

    /**
     * Save an image
     * 
     * @param array $input
     * @return boolean
     */
    public function save(array $input)
    {
        return $this->carGalleryMapper->persist($input);
    }
}
