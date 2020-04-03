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

use Krystal\Db\Sql\RawSqlFragment;
use Cms\Storage\MySQL\AbstractMapper;;
use Rentcar\Storage\ServiceMapperInterface;

final class ServiceMapper extends AbstractMapper implements ServiceMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_rentcar_services');
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationTable()
    {
        return ServiceTranslationMapper::getTableName();
    }

    /**
     * Returns shared columns to be selected
     * 
     * @return array
     */
    private function getColumns()
    {
        return array(
            self::column('id'),
            self::column('order'),
            self::column('price'),
            ServiceTranslationMapper::column('lang_id'),
            ServiceTranslationMapper::column('name'),
            ServiceTranslationMapper::column('description')
        );
    }

    /**
     * Fetch all extra services
     * 
     * @param boolean $sort Whether to sort by order
     * @return array
     */
    public function fetchAll($sort = false)
    {
        $db = $this->createEntitySelect($this->getColumns())
                   ->whereEquals(ServiceTranslationMapper::column('lang_id'), $this->getLangId());

        if ($sort == true) {
            $db->orderBy(new RawSqlFragment(sprintf('`order`, CASE WHEN `order` = 0 THEN %s END DESC', self::column('id'))));
        } else {
            $db->orderBy(self::column('id'))
               ->desc();
        }

        return $db->queryAll();
    }

    /**
     * Fetches a service by its id
     * 
     * @param int $id Service id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return mixed
     */
    public function fetchById($id, $withTranslations)
    {
        return $this->findEntity($this->getColumns(), $id, $withTranslations);
    }
}
