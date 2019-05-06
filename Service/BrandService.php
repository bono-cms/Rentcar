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

use Rentcar\Storage\BrandMapperInterface;
use Cms\Service\AbstractManager;
use Krystal\Stdlib\ArrayUtils;
use Krystal\Image\Tool\ImageManagerInterface;

final class BrandService extends AbstractManager
{
    /**
     * Any compliant brand mapper
     * 
     * @var \Rentcar\Storage\BrandMapperInterface
     */
    private $brandMapper;

    /**
     * Car image service
     * 
     * @var \Krystal\Image\Tool\ImageManagerInterface
     */
    private $imageService;

    /**
     * State initialization
     * 
     * @param \Rentcar\Storage\BrandMapperInterface $brandMapper
     * @param \Krystal\Image\Tool\ImageManagerInterface $imageService
     * @return void
     */
    public function __construct(BrandMapperInterface $brandMapper, ImageManagerInterface $imageService)
    {
        $this->brandMapper = $brandMapper;
        $this->imageService = $imageService;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $imageBag = clone $this->imageService->getImageBag();
        $imageBag->setId($row['id'])
                 ->setCover($row['icon']);

        $entity = new BrandEntity();
        $entity->setId($row['id'])
               ->setName($row['name'])
               ->setOrder($row['order'])
               ->setIcon($row['icon']);

        $entity->setImageBag($imageBag);

        return $entity;
    }

    /**
     * Returns last brand id
     * 
     * @return int
     */
    public function getLastId()
    {
        return $this->brandMapper->getMaxId();
    }

    /**
     * Saves a brand
     * 
     * @param array $input
     * @return boolean
     */
    public function save(array $input)
    {
        // References
        $file =& $input['files']['file'];
        $brand =& $input['data']['brand'];

        // If image file is selected
        if (!empty($file)) {
            // And finally append
            $brand['icon'] = $file->getUniqueName();
        }

        $this->brandMapper->persist($brand);

        if (!empty($file)) {
            // Grab ID depending on newness
            $id = !empty($brand['id']) ? $brand['id'] : $this->getLastId();

            // Now upload a new one
            $this->imageService->upload($id, $file);
        }

        return true;
    }

    /**
     * Deletes a brand by its id
     * 
     * @param int $id Brand id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->brandMapper->deleteByPk($id) && $this->imageService->delete($id);
    }

    /**
     * Fetch brand by its id
     * 
     * @param int $id Brand id
     * @return mixed
     */
    public function fetchById($id)
    {
        return $this->prepareResult($this->brandMapper->findByPk($id));
    }

    /**
     * Fetch brands as a list
     * 
     * @return array
     */
    public function fetchList()
    {
        return ArrayUtils::arrayList($this->brandMapper->fetchAll(false), 'id', 'name');
    }

    /**
     * Fetch all brands
     * 
     * @param boolean $sort Whether to sort brands by their sorting order
     * @return array
     */
    public function fetchAll($sort = false)
    {
        return $this->prepareResults($this->brandMapper->fetchAll($sort));
    }
}
