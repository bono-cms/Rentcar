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

use Rentcar\Storage\ServiceMapperInterface;
use Cms\Service\AbstractManager;
use Krystal\Stdlib\VirtualEntity;

final class RentService extends AbstractManager
{
    /**
     * Any compliant mapper
     * 
     * @var \Rentcar\Storage\ServiceMapperInterface
     */
    private $serviceMapper;

    /**
     * State initialization
     * 
     * @param \Rentcar\Storage\ServiceMapperInterface $serviceMapper
     * @return void
     */
    public function __construct(ServiceMapperInterface $serviceMapper)
    {
        $this->serviceMapper = $serviceMapper;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $entity = new VirtualEntity();
        $entity->setId($row['id'])
               ->setLangId($row['lang_id'])
               ->setPrice($row['price'])
               ->setOrder($row['order'])
               ->setName($row['name'])
               ->setDescription($row['description']);

        return $entity;
    }

    /**
     * Returns last service id
     * 
     * @return mixed
     */
    public function getLastId()
    {
        return $this->serviceMapper->getMaxId();
    }

    /**
     * Persists a service
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function save(array $input)
    {
        return $this->serviceMapper->persist($input);
    }

    /**
     * Deletes a service by its id
     * 
     * @param int $id Service id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->serviceMapper->deleteByPk($id);
    }

    /**
     * Fetch all services
     * 
     * @param boolean $sort Whether to sort by order
     * @return array
     */
    public function fetchAll($sort = false)
    {
        return $this->prepareResults($this->serviceMapper->fetchAll($sort));
    }

    /**
     * Fetches a service by its id
     * 
     * @param int $id Service id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return mixed
     */
    public function fetchById($id, $withTranslations)
    {
        $data = $this->serviceMapper->fetchById($id, $withTranslations);

        if ($withTranslations) {
            return $this->prepareResults($data);
        } else {
            return $this->prepareResult($data);
        }
    }
}
