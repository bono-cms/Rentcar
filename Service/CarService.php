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
               ->setOrder($row['order'])
               ->setName($row['name'])
               ->setDescription($row['description']);

        return $entity;
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
