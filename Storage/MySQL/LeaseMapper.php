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
use Krystal\Db\Sql\RawSqlFragment;

final class LeaseMapper extends AbstractMapper implements LeaseMapperInterface
{
    /**
     * Shared date format
     * 
     * @var string
     */
    private $dateFormat = '%d.%m.%Y';

    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_rentcar_lease');
    }

    /**
     * Builds date format function to be used when selecting date column
     * 
     * @param string $column
     * @return string
     */
    private function formatDate($column)
    {
        return "DATE_FORMAT({$column}, '{$this->dateFormat}') AS {$column}";
    }

    /**
     * Formats string as a date
     * 
     * @param string $target Input date
     * @return \Krystal\Db\Sql\RawSqlFragment
     */
    private function strToDate($target)
    {
        return new RawSqlFragment("STR_TO_DATE('{$target}', '{$this->dateFormat}')");
    }

    /**
     * Saves lease item
     * 
     * @param array $input
     * @return boolean
     */
    public function save(array $input)
    {
        // Override date columns with different format
        $input['apply_date'] = $this->strToDate($input['apply_date']);
        $input['run_date'] = $this->strToDate($input['run_date']);

        return $this->persist($input);
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
     * Fetch lease data by its id
     * 
     * @param int $id Lease id
     * @return array
     */
    public function fetchById($id)
    {
        // Columbs to be selected
        $columns = array(
            '*',
            // Override date columns with different format
            $this->formatDate('apply_date'),
            $this->formatDate('run_date')
        );

        return $this->fetchByColumn($this->getPk(), $id, join(', ', $columns));
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
