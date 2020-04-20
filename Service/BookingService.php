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

use InvalidArgumentException;
use Krystal\Stdlib\VirtualEntity;
use Krystal\Db\Filter\FilterableServiceInterface;
use Krystal\Date\TimeHelper;
use Krystal\Text\TextUtils;
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
               ->setExtension($row['extension'])
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
     * Get availability count
     * 
     * @return int
     */
    public function getAvailabilityCount()
    {
        $total = (int) $this->bookingMapper->getTotalCarCount();
        $taken = (int) $this->bookingMapper->getTakenCount(TimeHelper::getNow());

        return $total - $taken;
    }

    /**
     * Confirms that payment is done by token
     * 
     * @param string $token Transaction token
     * @return boolean Depending on success
     */
    public function confirmPayment($token)
    {
        return $this->bookingMapper->updateStatusByToken($token, OrderStatusCollection::STATUS_APPROVED);
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
            $total = 0;
            $collection = new OrderStatusCollection();

            foreach ($rows as $row) {
                $output[] = [
                    'status' => $collection->findByKey($row['status']),
                    'count' => $row['count']
                ];

                $total += $row['count'];
            }

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
        if (!TimeHelper::formatValid($datetime)) {
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
        if (!TimeHelper::formatValid($checkin) || !TimeHelper::formatValid($checkout)) {
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
     * @return array|boolean Depending on success
     */
    public function createNew(array $input)
    {
        // Double check if car is available
        $availability = $this->carAvailability($input['car_id'], $input['checkin'], $input['checkout']);

        if ($availability['available'] === true) {
            $input['status'] = OrderStatusCollection::STATUS_NEW;
            $input['datetime'] = TimeHelper::getNow();
            $input['token'] = TextUtils::uniqueString(); // Unique token of this transaction

            if ($this->bookingMapper->persist($input)) {
                return $this->bookingMapper->findByToken($input['token']);
            } else {
                return false;
            }

        } else {
            return false;
        }
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
     * Finds row by its associated token
     * 
     * @param string $token
     * @return array
     */
    public function fetchByToken($token)
    {
        return $this->prepareResult($this->bookingMapper->findByToken($token));
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
