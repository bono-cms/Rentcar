<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

return array(
    '/%s/module/rent-car/cars' => array(
        'controller' => 'Admin:Car@indexAction'
    ),

    '/%s/module/rent-car/cars/save' => array(
        'controller' => 'Admin:Car@saveAction'
    ),

    '/%s/module/rent-car/cars/add' => array(
        'controller' => 'Admin:Car@addAction'
    ),

    '/%s/module/rent-car/cars/edit/(:var)' => array(
        'controller' => 'Admin:Car@editAction'
    ),

    '/%s/module/rent-car/cars/delete/(:var)' => array(
        'controller' => 'Admin:Car@deleteAction'
    )
);
