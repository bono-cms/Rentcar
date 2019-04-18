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

use Rentcar\Storage\CarMapperInterface;
use Cms\Service\AbstractManager;
use Krystal\Stdlib\VirtualEntity;

final class CarService extends AbstractManager
{
    /**
     * Any compliant car mapper
     * 
     * @var \Rentcar\Storage\CarMapperInterface
     */
    private $carMapper;

    /**
     * State initialization
     * 
     * @param \Rentcar\Storage\CarMapperInterface $carMapper
     * @return void
     */
    public function __construct(CarMapperInterface $carMapper)
    {
        $this->carMapper = $carMapper;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $entity = new VirtualEntity();
        $entity->setId($row['id'])
               ->setLangId($row['lang_id'])
               ->setWebPageId($row['web_page_id'])
               ->setBrandId($row['brand_id'])
               ->setBrand(isset($row['brand']) ? $row['brand'] : null)
               ->setOrder($row['order'])
               ->setName($row['name'])
               ->setDescription($row['description'])
               ->setInterior($row['interior'])
               ->setExterior($row['exterior']);

        return $entity;
    }

    /**
     * Returns last car id
     * 
     * @return int
     */
    public function getLastId()
    {
        return $this->carMapper->getMaxId();
    }

    /**
     * Deletes a car by its id
     * 
     * @param string $id Car id
     * @return void
     */
    public function deleteById($id)
    {
        return $this->carMapper->deleteByPk($id);
    }

    /**
     * Saves a car
     * 
     * @param array $input
     * @return boolean
     */
    public function save(array $input)
    {
        return $this->carMapper->saveEntity($input['car'], $input['translation']);
    }

    /**
     * Fetch all cars
     * 
     * @return array
     */
    public function fetchAll()
    {
        return $this->prepareResults($this->carMapper->fetchAll());
    }

    /**
     * Fetches car data by its associated id
     * 
     * @param string $id Car id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return array
     */
    public function fetchById($id, $withTranslations)
    {
        if ($withTranslations) {
            return $this->prepareResults($this->carMapper->fetchById($id, true));
        } else {
            return $this->prepareResult($this->carMapper->fetchById($id, false));
        }
    }
}
