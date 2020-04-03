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

use Rentcar\Storage\ServiceMapperInterface;
use Cms\Service\AbstractManager;
use Krystal\Stdlib\VirtualEntity;

final class RentService extends AbstractManager
{
    /**
     * Any compliant mapper
     * 
     * @var \Rentcar\Storage\ServiceMapperInterface
     */
    private $serviceMapper;

    /**
     * State initialization
     * 
     * @param \Rentcar\Storage\ServiceMapperInterface $serviceMapper
     * @return void
     */
    public function __construct(ServiceMapperInterface $serviceMapper)
    {
        $this->serviceMapper = $serviceMapper;
    }
}
