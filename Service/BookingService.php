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

final class BookingService extends AbstractManager
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
               ->setDatetime($row['datetime'])
               ->setName($row['name'])
               ->setGender($row['gender'])
               ->setEmail($row['email'])
               ->setComment($row['comment'])
               ->setPickup($row['pickup'])
               ->setReturn($row['return'])
               ->setCheckin($row['checkin'])
               ->setCheckout($row['checkout']);

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
     * Fetch all bookings
     * 
     * @param int $page Current page number
     * @param int $limit Per page limit
     * @return array
     */
    public function fetchAll($page = null, $limit = null)
    {
        return $this->prepareResults($this->bookingMapper->fetchAll($page, $limit));
    }
}
