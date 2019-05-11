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

use Cms\Service\AbstractManager;
use Rentcar\Storage\CarModificationMapperInterface;
use Krystal\Stdlib\VirtualEntity;

final class CarModificationService extends AbstractManager
{
    /**
     * Any compliant car modification mapper
     * 
     * @var \Rentcar\Storage\CarModificationMapperInterface
     */
    private $carModificationMapper;

    /**
     * State initialization
     * 
     * @param \Rentcar\Storage\CarModificationMapperInterface $carModificationMapper
     * @return void
     */
    public function __construct(CarModificationMapperInterface $carModificationMapper)
    {
        $this->carModificationMapper = $carModificationMapper;
    }

    /**
     * Parse raw prices
     * 
     * @param array $prices Raw price data
     * @return array
     */
    private static function parsePrices(array $prices)
    {
        $output = array();

        foreach ($prices as $price) {
            $output[] = array(
                'name' => $price['car'],
                'price' => $price['price'],
                'car_id' => $price['id']
            );
        }

        return $output;
    }

    /**
     * Parse raw modification data
     * 
     * @param array $modifications Raw modification data
     * @return array
     */
    private static function parseModifications(array $modifications)
    {
        $output = array();

        foreach ($modifications as $modification) {
            $output[] = array(
                'name' => sprintf('%s (%s)', $modification['car'], $modification['name']),
                'price' => $modification['price'],
                'car_id' => $modification['car_id']
            );
        }

        return $output;
    }

    /**
     * Get global prices
     * 
     * @param mixed $carId Optional car ID constraint
     * @return array
     */
    public function getPrices($carId = null)
    {
        $prices = self::parsePrices($this->carModificationMapper->fetchAllPrices($carId));
        $modification = self::parseModifications($this->carModificationMapper->fetchAll($carId));

        $merged = array_merge($prices, $modification);

        // Sort by shared car ID value
        usort($merged, function($a, $b) {
            return $a['car_id'] - $b['car_id'];
        });

        return $merged;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $entity = new VirtualEntity();
        $entity->setId($row['id'])
               ->setCarId($row['car_id'])
               ->setLangId($row['lang_id'])
               ->setName($row['name'])
               ->setPrice($row['price']);

        if (isset($row['car'])) {
            $entity->setCar($row['car']);
        }

        return $entity;
    }

    /**
     * Returns max id
     * 
     * @return int
     */
    public function getLastId()
    {
        return $this->carModificationMapper->getMaxId();
    }

    /**
     * Deletes a modification by its id
     * 
     * @param mixed $id Modification id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->carModificationMapper->deleteByPk($id);
    }

    /**
     * Saves car modification
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function save(array $input)
    {
        return $this->carModificationMapper->saveEntity($input['modification'], $input['translation']);
    }

    /**
     * Fetch car modification by its id
     * 
     * @param int $id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return array
     */
    public function fetchById($id, $withTranslations)
    {
        if ($withTranslations) {
            return $this->prepareResults($this->carModificationMapper->fetchById($id, true));
        } else {
            return $this->prepareResult($this->carModificationMapper->fetchById($id, false));
        }
    }

    /**
     * Fetch all modifications by associated car id
     * 
     * @param int $carId
     * @return array
     */
    public function fetchAll($carId)
    {
        return $this->prepareResults($this->carModificationMapper->fetchAll($carId));
    }
}
