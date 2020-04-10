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

use Rentcar\Storage\CarMapperInterface;
use Cms\Service\AbstractManager;
use Cms\Service\WebPageManagerInterface;
use Krystal\Image\Tool\ImageManagerInterface;
use Krystal\Stdlib\ArrayUtils;

final class CarService extends AbstractManager
{
    /**
     * Any compliant car mapper
     * 
     * @var \Rentcar\Storage\CarMapperInterface
     */
    private $carMapper;

    /**
     * Web page manager is responsible for managing slugs
     * 
     * @var \Cms\Service\WebPageManagerInterface
     */
    private $webPageManager;

    /**
     * Car image service
     * 
     * @var \Krystal\Image\Tool\ImageManagerInterface
     */
    private $imageService;

    /**
     * State initialization
     * 
     * @param \Rentcar\Storage\CarMapperInterface $carMapper
     * @param \Cms\Service\WebPageManagerInterface $webPageManager
     * @param \Krystal\Image\Tool\ImageManagerInterface $carService
     * @return void
     */
    public function __construct(CarMapperInterface $carMapper, WebPageManagerInterface $webPageManager, ImageManagerInterface $imageService)
    {
        $this->carMapper = $carMapper;
        $this->webPageManager = $webPageManager;
        $this->imageService = $imageService;
    }

    /**
     * Returns a collection of switching URLs
     * 
     * @param string $id Car ID
     * @return array
     */
    public function getSwitchUrls($id)
    {
        return $this->carMapper->createSwitchUrls($id, 'Rentcar', 'Rentcar:Car@carAction');
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $entity = new CarEntity();
        $entity->setId($row['id'])
               ->setLangId($row['lang_id'])
               ->setWebPageId($row['web_page_id'])
               ->setBrandId($row['brand_id'])
               ->setPrice($row['price'])
               ->setBrand(isset($row['brand']) ? $row['brand'] : null)
               ->setOrder($row['order'])
               ->setImage($row['image'])
               ->setQty($row['qty'])
               ->setRent($row['rent'])
               ->setName($row['name'])
               ->setDescription($row['description'])
               ->setInterior($row['interior'])
               ->setExterior($row['exterior'])
               ->setFeatures($row['features'])
               ->setOptions($row['options'])
               /* Common front attributes */
               ->setCapacity($row['capacity'])
               ->setTransmission($row['transmission'])
               ->setSafety($row['safety'])
               ->setFuel($row['fuel'])
               ->setAirbags($row['airbags'])
               ->setSlug($row['slug'])
               ->setTitle($row['title'])
               ->setKeywords($row['keywords'])
               ->setMetaDescription($row['meta_description'])
               ->setUrl($this->webPageManager->surround($entity->getSlug(), $entity->getLangId()))
               ->setChangeFreq($row['changefreq'])
               ->setPriority($row['priority']);

        $imageBag = clone $this->imageService->getImageBag();
        $imageBag->setId($row['id'])
                 ->setCover($row['image']);

        $entity->setImageBag($imageBag);

        return $entity;
    }

    /**
     * Returns total quantity of all cars
     * 
     * @return int
     */
    public function getTotalQty()
    {
        return (int) $this->carMapper->getTotalQty();
    }

    /**
     * Returns total number of cars
     * 
     * @return int
     */
    public function getTotalCount()
    {
        return (int) $this->carMapper->getTotalCount();
    }

    /**
     * Returns last car id
     * 
     * @return int
     */
    public function getLastId()
    {
        return $this->carMapper->getMaxId();
    }

    /**
     * Deletes a car by its id
     * 
     * @param string $id Car id
     * @return void
     */
    public function deleteById($id)
    {
        // Delete row and its image (if present)
        return $this->carMapper->deleteByPk($id) && $this->imageService->delete($id);
    }

    /**
     * Delete many cars by their ids
     * 
     * @param array $ids
     * @return boolean
     */
    public function deleteByIds(array $ids)
    {
        return $this->carMapper->deleteByPks($ids) && $this->imageService->deleteMany($ids);
    }

    /**
     * Saves a car
     * 
     * @param array $input
     * @return boolean
     */
    public function save(array $input)
    {
        // References
        $file =& $input['files']['file'];
        $data =& $input['data'];

        // If image file is selected
        if (!empty($file)) {
            // And finally append
            $data['car']['image'] = $file->getUniqueName();
        }

        // Persist a car
        $this->carMapper->savePage('Rentcar', 'Rentcar:Car@carAction', $data['car'], $data['translation']);

        // Grab ID depending on newness
        $id = !empty($data['car']['id']) ? $data['car']['id'] : $this->getLastId();

        if (!empty($file)) {
            // Now upload a new one
            $this->imageService->upload($id, $file);
        }

        // Save service relation
        $this->carMapper->saveServiceRelation($id, isset($data['services']) ? $data['services'] : []);

        return true;
    }

    /**
     * Fetches hash map
     * 
     * @return array
     */
    public function fetchList()
    {
        return ArrayUtils::arrayList($this->carMapper->fetchAll(null, null), 'id', 'name');
    }

    /**
     * Fetch all cars
     * 
     * @param int $page Optional page number
     * @param int $limit Optional per page limit
     * @return array
     */
    public function fetchAll($page = null, $limit = null)
    {
        return $this->prepareResults($this->carMapper->fetchAll($page, $limit));
    }

    /**
     * Fetches car data by its associated id
     * 
     * @param string $id Car id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return array
     */
    public function fetchById($id, $withTranslations)
    {
        if ($withTranslations) {
            return $this->prepareResults($this->carMapper->fetchById($id, true));
        } else {
            return $this->prepareResult($this->carMapper->fetchById($id, false));
        }
    }
}
