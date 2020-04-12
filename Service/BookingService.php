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

use Datetime;
use Exception;
use InvalidArgumentException;
use Krystal\Stdlib\VirtualEntity;
use Krystal\Db\Filter\FilterableServiceInterface;
use Krystal\Date\TimeHelper;
use Cms\Service\AbstractManager;
use Rentcar\Storage\BookingMapperInterface;
use Rentcar\Storage\BookingServiceMapperInterface;
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
     * Booking mapper
     * 
     * @var \Rentcar\Storage\BookingServiceMapperInterface
     */
    private $bookingServiceMapper;

    /**
     * State initialization
     * 
     * @param \Rentcar\Storage\BookingMapperInterface $bookingMapper
     * @param \Rentcar\Storage\BookingServiceMapperInterface $bookingServiceMapper
     * @return void
     */
    public function __construct(BookingMapperInterface $bookingMapper, BookingServiceMapperInterface $bookingServiceMapper)
    {
        $this->bookingMapper = $bookingMapper;
        $this->bookingServiceMapper = $bookingServiceMapper;
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
               ->setCurrency(isset($row['currency']) ? $row['currency'] : null)
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

        // Set formated price
        if (isset($row['amount'], $row['currency'])) {
            $entity->setPrice(number_format($row['amount']) . ' ' . $row['currency']);
        }

        if (isset($row['image'])) {
            $entity->setImage(self::createImagePath($row['car_id'], $row['image']));
        }

        return $entity;
    }

    /**
     * Fetch all booking services by associated booking id
     * 
     * @param int $bookingId
     * @return array
     */
    public function fetchServices($bookingId)
    {
        $rows = $this->bookingServiceMapper->fetchAll($bookingId);

        // Append currency to price
        foreach ($rows as &$row) {
            $row['price'] = sprintf('%s %s', $row['amount'], $row['currency']);
        }

        return $rows;
    }

    /**
     * Count statuses
     * 
     * @return array|boolean
     */
    public function getStatusSummary()
    {
        $rows = $this->bookingMapper->getStatusSummary();

        if ($rows) {
            $output = [];
            $collection = new OrderStatusCollection();

            foreach ($rows as $row) {
                $output[$row['count']] = $collection->findByKey($row['status']);
            }

            // Total number of orders
            $total = array_sum(array_keys($output));

            return [
                'total' => $total,
                'summary' => $output
            ];

        } else {
            return false;
        }
    }

    /**
     * Counts total sum with corresponding currencies
     * 
     * @return array|boolean
     */
    public function getAmountSummary()
    {
        $rows = $this->bookingMapper->getAmountSummary();

        if ($rows) {
            $output = [];

            foreach ($rows as $row) {
                $output[] = sprintf('%s %s', number_format($row['amount']), $row['currency']);
            }

            return $output;
        } else {
            return false;
        }
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
     * Creates image path
     * 
     * @param int $carId
     * @param string $image
     * @return string
     */
    private static function createImagePath($carId, $image)
    {
        return sprintf('%s/%s/350x350/%s', Module::IMG_PATH_CARS, $carId, $image);
    }

    /**
     * Checks whether datetime format is valid
     * 
     * @param string $datetime
     * @return boolean
     */
    private static function formatValid($datetime)
    {
        try {
            new Datetime($datetime);
            return true;
        } catch(Exception $e){
            return false;
        }
    }

    /**
     * Appends meta data
     * 
     * @param array $data
     * @return array
     */
    private static function withMeta(array $data)
    {
        $data['free'] = intval($data['qty'] - $data['taken']);
        $data['available'] = $data['free'] >= 1;

        if (isset($data['image'])) {
            $data['image'] = self::createImagePath($data['id'], $data['image']);
        }

        return $data;
    }
    
    /**
     * Fetch cars with booking status
     * 
     * @param string $datetime
     * @throws \InvalidArgumentException On wrong date format
     * @return array
     */
    public function fetchCars($datetime)
    {
        if (!self::formatValid($datetime)) {
            throw new InvalidArgumentException('Invalid datetime format provided');
        }

        $rows = $this->bookingMapper->fetchCars($datetime);

        foreach ($rows as &$row) {
            $row = self::withMeta($row);
        }

        return $rows;
    }

    /**
     * Get car availability information
     * 
     * @param int $carId
     * @param string $checkin
     * @param string $checkout
     * @throws \InvalidArgumentException On wrong date format
     * @return array|boolean
     */
    public function carAvailability($carId, $checkin, $checkout)
    {
        if (!self::formatValid($checkin) || !self::formatValid($checkout)) {
            throw new InvalidArgumentException('Invalid datetime format provided');
        }

        $data = $this->bookingMapper->carAvailability($carId, $checkin, $checkout);

        if (!empty($data)) {
            return self::withMeta($data);
        } else {
            // Invalid car ID supplied
            return false;
        }
    }

    /**
     * Create new booking
     * 
     * @param array $input
     * @param array $serviceIds
     * @return boolean Depending on success
     */
    public function createNew(array $input)
    {
        // Double check if car is available
        $availability = $this->carAvailability($input['car_id'], $input['checkin'], $input['checkout']);

        if ($availability['available'] === true) {
            $input['status'] = OrderStatusCollection::STATUS_NEW;
            $input['datetime'] = TimeHelper::getNow();

            return $this->save($input);
        } else {
            return false;
        }
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
