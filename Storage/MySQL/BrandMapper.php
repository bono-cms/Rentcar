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
use Rentcar\Storage\BrandMapperInterface;

final class BrandMapper extends AbstractMapper implements BrandMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_rentcar_brands');
    }

    /**
     * Fetch all brands
     * 
     * @param boolean $sort Whether to sort brands by their sorting order
     * @return array
     */
    public function fetchAll($sort)
    {
        $db = $this->db->select('*')
                       ->from(self::getTableName());

        if ($sort === true) {
            $db->orderBy('order');
        } else {
            $db->orderBy($this->getPk())
               ->desc();
        }

        return $db->queryAll();
    }
}
