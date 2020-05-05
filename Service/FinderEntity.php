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

use Krystal\Stdlib\VirtualEntity;
use DateTime;

final class FinderEntity extends VirtualEntity
{
    /**
     * Creates finder entity instance from query string
     * 
     * @param array $query Query array received from GET
     * @return \Rentcar\Service\FinderEntity
     */
    public static function factory(array $query)
    {
        // Safely retrieves values from query array
        $get = function($group, $key) use ($query){
            return isset($query[$group][$key]) ? $query[$group][$key] : null;
        };

        $entity = new self();
        $entity->setPickupLocation($get('pickup', 'location'))
               ->setPickupDate($get('pickup', 'date'))
               ->setPickupTime($get('pickup', 'time'))
               ->setReturnLocation($get('return', 'location'))
               ->setReturnDate($get('return', 'date'))
               ->setReturnTime($get('return', 'time'))
               // Sometimes might be defined
               ->setCheckin($get('pickup', 'checkin'))
               ->setCheckout($get('return', 'checkout'));

        return $entity;
    }

    /**
     * Returns data as columns
     * 
     * @return array
     */
    public function getAsColumns()
    {
        return [
            'pickup' => $this->getPickupLocation(),
            'checkin' => $this->getCheckin(),
            'return' => $this->getReturnLocation(),
            'checkout' => $this->getCheckout()
        ];
    }

    /**
     * Counts a price
     * 
     * @param float $amount Daily price
     * @param boolean Whether to format output
     * @return string
     */
    public function countPrice($amount, $format = true)
    {
        $price = $amount * $this->getPeriod();

        if ($format == true) {
            $price = number_format($price);
        }

        return $price;
    }

    /**
     * Normalizes date and time
     * 
     * @param string $date
     * @param string $time
     * @return string
     */
    private static function normalizeTime($date, $time)
    {
        $time = new DateTime($time);

        $hour = $time->format('H');
        $minute = $time->format('i');
        $second = $time->format('s');

        $date = new DateTime($date);
        $date->setTime($hour, $minute, $second);

        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Returns total rental period
     * 
     * @return int
     */
    public function getPeriod()
    {
        $checkin = new DateTime($this->getCheckin());
        $checkout = new DateTime($this->getCheckout());

        // Returns difference in days
        return $checkout->diff($checkin)->format("%a");
    }

    /** 
     * Returns checkout date and time
     * 
     * @return string
     */
    public function getCheckout()
    {
        if ($this->has('checkout')) {
            return $this->get('checkout');
        }

        return self::normalizeTime($this->getReturnDate(), $this->getReturnTime());
    }

    /** 
     * Returns checkout date and time
     * 
     * @return string
     */
    public function getCheckin()
    {
        if ($this->has('checkin')) {
            return $this->get('checkin');
        }

        return self::normalizeTime($this->getPickupDate(), $this->getPickupTime());
    }

    /**
     * Checks if entire entity is populated
     * 
     * @return boolean
     */
    public function isPopulated()
    {
        $filtered = array_filter($this->getAsColumns(), function($value){
            return empty($value);
        });

        return empty($filtered);
    }
}
