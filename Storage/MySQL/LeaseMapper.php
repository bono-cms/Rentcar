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
use Rentcar\Storage\LeaseMapperInterface;

final class LeaseMapper extends AbstractMapper implements LeaseMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_rentcar_lease');
    }

    /**
     * Fetch available car models
     * 
     * @return array
     */
    public function fetchModels()
    {
        $column = 'model';

        $db = $this->db->select($column, true)
                       ->from(self::getTableName())
                       ->orderBy($this->getPk())
                       ->desc();

        return $db->queryAll($column);
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
    public function filter($input, $page, $itemsPerPage, $sortingColumn, $desc)
    {
        $sortingColumns = array(
            'owner' => self::column('owner'),
            'model' => self::column('model')
        );

        // Current sorting column
        $sortingColumn = isset($sortingColumn[$sortingColumn]) ? $sortingColumn[$sortingColumn] : self::column($this->getPk());

        if (!$sortingColumn) {
            $sortingColumn = $this->getPk();
        }

        $db = $this->db->select('*')
                       ->from(self::getTableName())
                       ->whereEquals('1', '1')
                       ->andWhereLike(self::column('owner'), '%'.$input['owner'].'%', true)
                       ->andWhereLike(self::column('model'), '%'.$input['model'].'%', true)
                       ->andWhereLike(self::column('numberplate'), '%'.$input['numberplate'].'%', true)
                       ->andWhereLike(self::column('contract_number'), '%'.$input['contract_number'].'%', true)
                       ->andWhereEquals(self::column('period'), $input['period'], true)
                       ->orderBy($sortingColumn);

        if ($desc) {
            $db->desc();
        }

        return $db->paginate($page, $itemsPerPage)
                  ->queryAll();
    }

    /**
     * Fetch all lease items
     * 
     * @return array
     */
    public function fetchAll()
    {
        $db = $this->db->select('*')
                       ->from(self::getTableName())
                       ->orderBy($this->getPk())
                       ->desc();

        return $db->queryAll();
    }
}
