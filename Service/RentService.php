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

use Rentcar\Storage\RentServiceMapperInterface;
use Rentcar\Collection\RentServiceUnitCollection;
use Cms\Service\AbstractManager;
use Krystal\Stdlib\VirtualEntity;
use Krystal\Stdlib\ArrayUtils;

final class RentService extends AbstractManager
{
    /**
     * Any compliant mapper
     * 
     * @var \Rentcar\Storage\RentServiceMapperInterface
     */
    private $serviceMapper;

    /**
     * State initialization
     * 
     * @param \Rentcar\Storage\RentServiceMapperInterface $serviceMapper
     * @return void
     */
    public function __construct(RentServiceMapperInterface $serviceMapper)
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
               ->setUnit($row['unit'])
               ->setName($row['name'])
               ->setDescription($row['description']);

        return $entity;
    }

    /**
     * Save booking services
     * 
     * @param int $bookingId
     * @param array $serviceIds
     * @param int $period
     * @return boolean
     */
    public function saveBooking($bookingId, array $serviceIds, $period)
    {
        $services = $this->fetchByIds($serviceIds);
        $values = [];

        foreach ($services as $service) {
            $values[] = [
                $bookingId,
                $service->getId(),
                $service->getPrice() * intval($period)
            ];
        }

        return $this->serviceMapper->saveBooking($values);
    }

    /**
     * Counts total amount of extra services
     * 
     * @param array $ids Service ids
     * @param int $period Rental period (number of days)
     * @return float
     */
    public function countAmount(array $ids, $period)
    {
        $services = $this->fetchByIds($ids);
        $amount = 0;

        foreach ($services as $service) {
            if ($service->getPrice() != 0) {
                if ($service->getUnit() == RentServiceUnitCollection::UNIT_DAILY) {
                    $amount += ($service->getPrice() * $period);
                }

                if ($service->getUnit() == RentServiceUnitCollection::UNIT_ONCE) {
                    $amount += $service->getPrice();
                }
            }
        }

        return $amount;
    }

    /**
     * Append services
     * 
     * @param \Rentcar\Service\CarEntity
     * @return void
     */
    public function appendServices(CarEntity $car)
    {
        $serviceIds = $this->fetchAttachedIds($car->getId());
        $services = !empty($serviceIds) ? $this->fetchByIds($serviceIds) : [];

        $car->setServices($services)
            ->setServiceCount(count($services));
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
        return $this->serviceMapper->saveEntity($input['service'], $input['translation']);
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
     * Fetch attaches service Ids by associated car ID
     * 
     * @param int $id Car id
     * @return array
     */
    public function fetchAttachedIds($carId)
    {
        return $this->serviceMapper->fetchAttachedIds($carId);
    }

    /**
     * Fetch all services as a hash map
     * 
     * @return array
     */
    public function fetchList()
    {
        return ArrayUtils::arrayList($this->serviceMapper->fetchAll(false), 'id', 'name');
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
     * Fetch services by their ids
     * 
     * @param array $ids
     * @return array
     */
    public function fetchByIds(array $ids)
    {
        return $this->prepareResults($this->serviceMapper->fetchByIds($ids));
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
