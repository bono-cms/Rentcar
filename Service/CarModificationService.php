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

    /**
     * Returns max id
     * 
     * @return int
     */
    public function getLastId()
    {
        return $this->carModificationMapper->getMaxId();
    }

    /**
     * Deletes a modification by its id
     * 
     * @param mixed $id Modification id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->carModificationMapper->deleteByPk($id);
    }

    /**
     * Saves car modification
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function save(array $input)
    {
        return $this->carModificationMapper->saveEntity($input['modification'], $input['translation']);
    }

    /**
     * Fetch car modification by its id
     * 
     * @param int $id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return array
     */
    public function fetchById($id, $withTranslations)
    {
        if ($withTranslations) {
            return $this->prepareResults($this->fetchById($id, true));
        } else {
            return $this->prepareResult($this->fetchById($id, false));
        }
    }

    /**
     * Fetch all modifications by associated car id
     * 
     * @param int $carId
     * @return array
     */
    public function fetchAll($carId)
    {
        return $this->prepareResults($this->carModificationMapper->fetchAll($carId));
    }
}
