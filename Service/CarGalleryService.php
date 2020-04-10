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
}
