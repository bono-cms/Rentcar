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

use Rentcar\Storage\LeaseMapperInterface;
use Cms\Service\AbstractManager;
use Krystal\Stdlib\VirtualEntity;
use Krystal\Db\Filter\FilterableServiceInterface;

final class LeaseService extends AbstractManager implements FilterableServiceInterface
{
    /**
     * Any compliant lease mapper interface
     * 
     * @var \Rentcar\Storage\LeaseMapperInterface
     */
    private $leaseMapper;

    /**
     * State initialization
     * 
     * @param \Rentcar\Storage\LeaseMapperInterface $leaseMapper
     * @return void
     */
    public function __construct(LeaseMapperInterface $leaseMapper)
    {
        $this->leaseMapper = $leaseMapper;
    }

    /**
     * Returns prepare pagination instance
     * 
     * @return \Krystal\Paginate\Paginator
     */
    public function getPaginator()
    {
        return $this->leaseMapper->getPaginator();
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $entity = new VirtualEntity();
        $entity->setId($row['id'])
               ->setOwner($row['owner'])
               ->setModel($row['model'])
               ->setNumberplate($row['numberplate'])
               ->setContractNumber($row['contract_number'])
               ->setApplyDate($row['apply_date'])
               ->setRunDate($row['run_date'])
               ->setContractLeaseNumber($row['contract_lease_number'])
               ->setPeriod($row['period'])
               ->setStatus($row['status'])
               ->setCityApplied($row['city_applied'])
               ->setCityOwner($row['city_owner'])
               ->setComment($row['comment']);

        return $entity;
    }

    /**
     * Fetch leasing contract by its id
     * 
     * @param array $id Lease id
     * @return mixed
     */
    public function fetchById($id)
    {
        return $this->prepareResult($this->leaseMapper->findByPk($id));
    }

    /**
     * Filters the raw input
     * 
     * @param array|\ArrayAccess $input Raw input data
     * @param integer $page Current page number
     * @param integer $itemsPerPage Items per page to be displayed
     * @param string $sortingColumn Column name to be sorted
     * @param string $desc Whether to sort in DESC order
     * @return array
     */
    public function filter($input, $page, $itemsPerPage, $sortingColumn, $desc, array $params = array())
    {
        return $this->prepareResults($this->leaseMapper->filter($input, $page, $itemsPerPage, $sortingColumn, $desc));
    }

    /**
     * Fetch all lease items
     * 
     * @return array
     */
    public function fetchAll()
    {
        return $this->prepareResults($this->leaseMapper->fetchAll());
    }

    /**
     * Returns last lease id
     * 
     * @return int
     */
    public function getLastId()
    {
        return $this->leaseMapper->getMaxId();
    }

    /**
     * Deletes lease item by its id
     * 
     * @param int $id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->leaseMapper->deleteByPk($id);
    }

    /**
     * Saves lease item
     * 
     * @param array $input
     * @return boolean
     */
    public function save(array $input)
    {
        return $this->leaseMapper->persist($input);
    }
}
