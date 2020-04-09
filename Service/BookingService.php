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

use Rentcar\Storage\BookingMapperInterface;
use Cms\Service\AbstractManager;
use Krystal\Stdlib\VirtualEntity;
use Krystal\Db\Filter\FilterableServiceInterface;
use Krystal\Date\TimeHelper;
use Rentcar\Module;
use Rentcar\Collection\OrderStatusCollection;

final class BookingService extends AbstractManager implements FilterableServiceInterface
{
    /**
     * Booking mapper
     * 
     * @var \Rentcar\Storage\BookingMapperInterface
     */
    private $bookingMapper;

    /**
     * State initialization
     * 
     * @param \Rentcar\Storage\BookingMapperInterface $bookingMapper
     * @return void
     */
    public function __construct(BookingMapperInterface $bookingMapper)
    {
        $this->bookingMapper = $bookingMapper;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $entity = new VirtualEntity();
        $entity->setId($row['id'])
               ->setCarId($row['car_id'])
               ->setStatus($row['status'])
               ->setAmount($row['amount'])
               ->setMethod($row['method'])
               ->setDatetime($row['datetime'])
               ->setName($row['name'])
               ->setGender($row['gender'])
               ->setEmail($row['email'])
               ->setPhone($row['phone'])
               ->setComment($row['comment'])
               ->setPickup($row['pickup'])
               ->setReturn($row['return'])
               ->setCheckin($row['checkin'])
               ->setCheckout($row['checkout']);

        if (isset($row['car'])) {
            $entity->setCar($row['car']);
        }

        if (isset($row['image'])) {
            $entity->setImage(sprintf('%s/%s/350x350/%s', Module::IMG_PATH_CARS, $row['car_id'], $row['image']));
        }

        return $entity;
    }

    /**
     * Returns last booking id
     * 
     * @return int
     */
    public function getLastId()
    {
        return $this->bookingMapper->getMaxId();
    }

    /**
     * Get car availability information
     * 
     * @param int $carId
     * @param string $checkin
     * @param string $checkout
     * @return array|boolean
     */
    public function carAvailability($carId, $checkin, $checkout)
    {
        $data = $this->bookingMapper->carAvailability($carId, $checkin, $checkout);

        if (!empty($data)) {
            // Append free and available keys
            $data['free'] = intval($data['qty'] - $data['taken']);
            $data['available'] = $data['free'] >= 1;

            return $data;
        } else {
            // Invalid car ID supplied
            return false;
        }
    }

    /**
     * Create new booking
     * 
     * @param array $input
     * @return boolean
     */
    public function createNew(array $input)
    {
        $input['status'] = OrderStatusCollection::STATUS_NEW;
        $input['datetime'] = TimeHelper::getNow();

        return $this->save($input);
    }

    /**
     * Update booking status
     * 
     * @param int $id Booking id
     * @param int $status Status constant
     * @return boolean
     */
    public function updateStatus($id, $status)
    {
        return $this->bookingMapper->updateStatus($id, $status);
    }

    /**
     * Save booking entry
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function save(array $input)
    {
        return $this->bookingMapper->persist($input);
    }

    /**
     * Count new orders
     * 
     * @return int
     */
    public function countNew()
    {
        return (int) $this->bookingMapper->countNew();
    }

    /**
     * Deletes booking entry by its id
     * 
     * @param int $id Booking id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->bookingMapper->deleteByPk($id);
    }

    /**
     * Fetches booking entry by its id
     * 
     * @param int $id Booking id
     * @return mixed
     */
    public function fetchById($id)
    {
        return $this->prepareResult($this->bookingMapper->findByPk($id));
    }

    /**
     * {@inheritDoc}
     */
    public function filter($input, $page, $itemsPerPage, $sortingColumn, $desc, array $parameters = [])
    {
        return $this->prepareResults($this->bookingMapper->filter($input, $page, $itemsPerPage, $sortingColumn, $desc, $parameters));
    }
}
