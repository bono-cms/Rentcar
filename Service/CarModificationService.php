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
use Rentcar\Storage\CarModificationMapperInterface;
use Krystal\Stdlib\VirtualEntity;

final class CarModificationService extends AbstractManager
{
    /**
     * Any compliant car modification mapper
     * 
     * @var \Rentcar\Storage\CarModificationMapperInterface
     */
    private $carModificationMapper;

    /**
     * State initialization
     * 
     * @param \Rentcar\Storage\CarModificationMapperInterface $carModificationMapper
     * @return void
     */
    public function __construct(CarModificationMapperInterface $carModificationMapper)
    {
        $this->carModificationMapper = $carModificationMapper;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $entity = new VirtualEntity();
        $entity->setId($row['id'])
               ->setCarId($row['car_id'])
               ->setLangId($row['lang_id'])
               ->setName($row['name'])
               ->setPrice($row['price']);

        return $entity;
    }
}
