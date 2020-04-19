<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Rentcar\Storage\MySQL;

use Cms\Storage\MySQL\AbstractMapper;
use Rentcar\Storage\BookingMapperInterface;
use Rentcar\Collection\OrderStatusCollection;
use Krystal\Db\Sql\QueryBuilder;
use Krystal\Db\Sql\RawSqlFragment;

final class BookingMapper extends AbstractMapper implements BookingMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_rentcar_booking');
    }

    /**
     * Returns total count
     * 
     * @return int
     */
    public function getTotalCarCount()
    {
        $db = $this->db->select()
                       ->sum('qty')
                       ->from(CarMapper::getTableName());

        return $db->queryScalar();
    }

    /**
     * Takes count of 
     * 
     * @param string $datetime
     * @return int
     */
    public function getTakenCount($datetime)
    {
        $column = new RawSqlFragment(sprintf("'%s'", $datetime));

        $db = $this->db->select()
                       ->count('id')
                       ->from(self::getTableName())
                       ->whereBetween($column, new RawSqlFragment('checkin'), new RawSqlFragment('checkout'));

        return $db->queryScalar();
    }

    /**
     * Count statuses
     * 
     * @return array
     */
    public function getStatusSummary()
    {
        $column = 'status';

        $db = $this->db->select($column)
                       ->count($column, 'count')
                       ->from(self::getTableName())
                       ->groupBy($column);

        return $db->queryAll();
    }

    /**
     * Counts total sum with corresponding currencies
     * 
     * @return array
     */
    public function getAmountSummary()
    {
        $db = $this->db->select('currency')
                       ->sum('amount', 'amount')
                       ->from(self::getTableName())
                       ->whereEquals('status', OrderStatusCollection::STATUS_APPROVED)
                       ->groupBy('currency');

        return $db->queryAll();
    }

    /**
     * Finds transaction by its associated token
     * 
     * @param string $token
     * @return array
     */
    public function findByToken($token)
    {
        return $this->fetchByColumn('token', $token);
    }

    /**
     * Fetch cars with booking status
     * 
     * @param string $datetime
     * @return array
     */
    public function fetchCars($datetime)
    {
        // Internal query
        $countQuery = function($datetime){
            $datetime = "'$datetime'";

            $qb = new QueryBuilder();
            $qb->select()
               ->count(BookingMapper::column('id'))
               ->from(BookingMapper::getTableName())
               ->whereEquals(BookingMapper::column('car_id'), CarMapper::column('id'))
               ->andWhereBetween(new RawSqlFragment($datetime), BookingMapper::column('checkin'), BookingMapper::column('checkout'));

            return $qb->getQueryString();
        };

        $db = $this->db->select([
                            CarMapper::column('id'),
                            CarMapper::column('qty'),
                            CarMapper::column('image'),
                            CarTranslationMapper::column('name'),
                            new RawSqlFragment(sprintf('(%s) AS taken', $countQuery($datetime)))
                        ])
                        ->from(CarMapper::getTableName())
                        // Car translation
                        ->leftJoin(CarTranslationMapper::getTableName(), [
                            CarTranslationMapper::column('id') => CarMapper::getRawColumn('id')
                        ])
                        // Booking relation
                        ->leftJoin(BookingMapper::getTableName(), [
                            BookingMapper::column('car_id') => CarMapper::getRawColumn('id')
                        ])
                        ->whereEquals(CarTranslationMapper::column('lang_id'), $this->getLangId())
                        ->orderBy(CarMapper::column('id'))
                        ->desc();

        return $db->queryAll();
    }

    /**
     * Get car availability information
     * 
     * @param int $carId
     * @param string $checkin
     * @param string $checkout
     * @return array
     */
    public function carAvailability($carId, $checkin, $checkout)
    {
        // Create date comparer constraint
        $dateConstraint = function($checkin, $checkout){
            // Quote raw strings
            $checkin = "'$checkin'";
            $checkout = "'$checkout'";

            $qb = new QueryBuilder();
            $qb->openBracket()
               ->openBracket()
               ->compare(self::column('checkin'), '>', $checkin)
               ->andWhere(self::column('checkout'), '<', $checkout)
               ->closeBracket()
               ->rawOr()
               ->openBracket()
               ->compare(self::column('checkin'), '<', $checkin)
               ->andWhere(self::column('checkout'), '>', $checkout)
               ->closeBracket()
               ->closeBracket();

            return $qb->getQueryString();
        };

        $db = $this->db->select([
                            CarMapper::column('qty')
                        ])
                       ->count(self::column('car_id'), 'taken')
                       ->from(CarMapper::getTableName())
                       // Car relation
                       ->leftJoin(self::getTableName(), [
                            self::column('car_id') => CarMapper::getRawColumn('id')
                        ])
                        ->rawAnd()
                        ->append('NOT')
                        ->append($dateConstraint($checkin, $checkout))
                        // Constraints
                        ->whereEquals(CarMapper::column('id'), $carId)
                        ->groupBy([
                            CarMapper::column('qty')
                        ]);

        return $db->query();
    }

    /**
     * Updates transaction status by its token
     * 
     * @param string $token Unique transaction token
     * @param int $status New status constant
     * @return boolean Depending on success
     */
    public function updateStatusByToken($token, $status)
    {
        $db = $this->db->update(self::getTableName(), ['status' => $status])
                       ->whereEquals('token', $token);

        return (bool) $db->execute(true);
    }

    /**
     * Count new orders
     * 
     * @return int
     */
    public function countNew()
    {
        $db = $this->db->select()
                       ->count('id')
                       ->from(self::getTableName())
                       ->whereEquals('status', OrderStatusCollection::STATUS_NEW);

        return $db->queryScalar();
    }

    /**
     * {@inheritDoc}
     */
    public function filter($input, $page, $itemsPerPage, $sortingColumn, $desc, array $parameters = [])
    {
        $sortingColumns = [
            'car_id' => self::column('car_id'),
            'status' => self::column('status'),
            'client' => self::column('client')
        ];

        // Current sorting column
        $sortingColumn = isset($sortingColumns[$sortingColumn]) ? $sortingColumns[$sortingColumn] : self::column($this->getPk());

        // If not defined explicitly, sort by PK
        if (!$sortingColumn) {
            $sortingColumn = $this->getPk();
        }

        // Columns to be selected
        $columns = [
            self::column('id'),
            self::column('car_id'),
            // Main details
            self::column('status'),
            self::column('extension'),
            self::column('amount'),
            self::column('datetime'),
            self::column('method'),
            // Client details
            self::column('name'),
            self::column('email'),
            self::column('phone'),
            self::column('gender'),
            self::column('comment'),
            // Order details
            self::column('pickup'),
            self::column('return'),
            self::column('checkin'),
            self::column('checkout'),
            // The rest
            CarMapper::column('image') => 'image',
            CarTranslationMapper::column('name') => 'car'
        ];

        $db = $this->db->select($columns)
                       ->from(self::getTableName())
                       // Car relation
                       ->leftJoin(CarMapper::getTableName(), [
                            CarMapper::column('id') => self::getRawColumn('car_id')
                       ])
                       // Car translation relation
                       ->leftJoin(CarTranslationMapper::getTableName(), [
                            CarTranslationMapper::column('id') => CarMapper::getRawColumn('id')
                       ])
                       // Language constraint
                       ->whereEquals(CarTranslationMapper::column('lang_id'), $this->getLangId())

                       // Filter constraints
                       ->andWhereEquals(self::column('car_id'), $input['car_id'], true)
                       ->andWhereEquals(self::column('status'), $input['status'], true)
                       ->andWhereLike(self::column('name'), '%'.$input['name'].'%', true);

        // Apply sorting
        $db->orderBy($sortingColumn);

        if ($desc) {
            $db->desc();
        }

        // Apply pagination if required
        if ($page !== null && $itemsPerPage !== null) {
            $db->paginate($page, $itemsPerPage);
        }

        return $db->queryAll();        
    }
}
