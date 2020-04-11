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
     * Returns checkout date and time
     * 
     * @return string
     */
    public function getCheckout()
    {
        return self::normalizeTime($this->getReturnDate(), $this->getReturnTime());
    }

    /** 
     * Returns checkout date and time
     * 
     * @return string
     */
    public function getCheckin()
    {
        return self::normalizeTime($this->getPickupDate(), $this->getPickupTime());
    }

    /**
     * Checks if entire entity is populated
     * 
     * @return boolean
     */
    public function isPopulated()
    {
        $filtered = array_filter($this->getProperties(), function($value){
            return empty($value);
        });

        return empty($filtered);
    }
}
