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

use Rentcar\Storage\BrandMapperInterface;
use Cms\Service\AbstractManager;
use Krystal\Stdlib\VirtualEntity;
use Krystal\Stdlib\ArrayUtils;

final class BrandService extends AbstractManager
{
    /**
     * Any compliant brand mapper
     * 
     * @var \Rentcar\Storage\BrandMapperInterface
     */
    private $brandMapper;

    /**
     * State initialization
     * 
     * @param \Rentcar\Storage\BrandMapperInterface $brandMapper
     * @return void
     */
    public function __construct(BrandMapperInterface $brandMapper)
    {
        $this->brandMapper = $brandMapper;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $entity = new VirtualEntity();
        $entity->setId($row['id'])
               ->setName($row['name'])
               ->setOrder($row['order'])
               ->setIcon($row['icon']);

        return $entity;
    }

    /**
     * Returns last brand id
     * 
     * @return int
     */
    public function getLastId()
    {
        return $this->brandMapper->getMaxId();
    }

    /**
     * Saves a brand
     * 
     * @param array $input
     * @return boolean
     */
    public function save(array $input)
    {
        return $this->brandMapper->persist($input);
    }

    /**
     * Deletes a brand by its id
     * 
     * @param int $id Brand id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->brandMapper->deleteByPk($id);
    }

    /**
     * Fetch brand by its id
     * 
     * @param int $id Brand id
     * @return mixed
     */
    public function fetchById($id)
    {
        return $this->prepareResult($this->brandMapper->findByPk($id));
    }

    /**
     * Fetch brands as a list
     * 
     * @return array
     */
    public function fetchList()
    {
        return ArrayUtils::arrayList($this->brandMapper->fetchAll(), 'id', 'name');
    }

    /**
     * Fetch all brands
     * 
     * @return array
     */
    public function fetchAll()
    {
        return $this->prepareResults($this->brandMapper->fetchAll());
    }
}
