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
    '/module/rent-cars/payment/count' => [
        'controller' => 'Car@countAction'
    ],

    '/module/rent-cars/payment/response/(:var)/' => array(
        'controller' => 'Car@responseAction'
    ),

    '/module/rent-cars/payment/gateway/(:var)' => array(
        'controller' => 'Car@gatewayAction'
    ),

    '/module/rent-cars/payment/finish/(:var)' => array(
        'controller' => 'Car@finishAction'
    ),

    '/module/rent-cars/list' => array(
        'controller' => 'Car@listAction'
    ),

    '/module/rent-car/book' => [
        'controller' => 'Car@bookAction'
    ],

    // Car modifications
    '/%s/module/rent-car/cars/modifications/edit/(:var)' => array(
        'controller' => 'Admin:CarModification@editAction'
    ),

    '/%s/module/rent-car/cars/modifications/add/(:var)' => array(
        'controller' => 'Admin:CarModification@addAction'
    ),

    '/%s/module/rent-car/cars/modifications/delete/(:var)' => array(
        'controller' => 'Admin:CarModification@deleteAction'
    ),

    '/%s/module/rent-car/cars/modifications/save' => array(
        'controller' => 'Admin:CarModification@saveAction'
    ),
    
    // Cars
    '/%s/module/rent-car' => array(
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
    ),

    // Brands
    '/%s/module/rent-car/brands' => array(
        'controller' => 'Admin:Brand@indexAction'
    ),
    
    '/%s/module/rent-car/brands/save' => array(
        'controller' => 'Admin:Brand@saveAction'
    ),

    '/%s/module/rent-car/brands/add' => array(
        'controller' => 'Admin:Brand@addAction'
    ),

    '/%s/module/rent-car/brands/edit/(:var)' => array(
        'controller' => 'Admin:Brand@editAction'
    ),

    '/%s/module/rent-car/brands/delete/(:var)' => array(
        'controller' => 'Admin:Brand@deleteAction'
    ),

    // Lease
    '/%s/module/rent-car/lease' => array(
        'controller' => 'Admin:Lease@indexAction'
    ),

    '/%s/module/rent-car/lease/save' => array(
        'controller' => 'Admin:Lease@saveAction'
    ),

    '/%s/module/rent-car/lease/add' => array(
        'controller' => 'Admin:Lease@addAction'
    ),

    '/%s/module/rent-car/lease/edit/(:var)' => array(
        'controller' => 'Admin:Lease@editAction'
    ),

    '/%s/module/rent-car/lease/delete/(:var)' => array(
        'controller' => 'Admin:Lease@deleteAction'
    ),

    '/%s/module/rent-car/lease/view/(:var)' => array(
        'controller' => 'Admin:Lease@viewAction'
    ),

    // Services
    '/%s/module/rent-car/services' => [
        'controller' => 'Admin:RentService@indexAction'
    ],

    '/%s/module/rent-car/services/add' => [
        'controller' => 'Admin:RentService@addAction'
    ],

    '/%s/module/rent-car/services/edit/(:var)' => [
        'controller' => 'Admin:RentService@editAction'
    ],

    '/%s/module/rent-car/services/delete/(:var)' => [
        'controller' => 'Admin:RentService@deleteAction'
    ],

    '/%s/module/rent-car/services/save' => [
        'controller' => 'Admin:RentService@saveAction'
    ],

    // Booking
    '/%s/module/rent-car/booking' => [
        'controller' => 'Admin:Booking@indexAction'
    ],

    '/%s/module/rent-car/booking/delete/(:var)' => [
        'controller' => 'Admin:Booking@deleteAction'
    ],

    '/%s/module/rent-car/booking/details/(:var)' => [
        'controller' => 'Admin:Booking@detailsAction'
    ],

    '/%s/module/rent-car/booking/add' => [
        'controller' => 'Admin:Booking@addAction'
    ],

    '/%s/module/rent-car/booking/edit/(:var)' => [
        'controller' => 'Admin:Booking@editAction'
    ],

    '/%s/module/rent-car/booking/save' => [
        'controller' => 'Admin:Booking@saveAction'
    ],

    '/%s/module/rent-car/booking/availability' => [
        'controller' => 'Admin:Booking@availabilityAction'
    ],

    '/%s/module/rent-car/booking/stat' => [
        'controller' => 'Admin:Booking@statAction'
    ],

    // Gallery
    '/%s/module/rent-car/gallery/add/(:var)' => [
        'controller' => 'Admin:CarGallery@addAction'
    ],

    '/%s/module/rent-car/gallery/edit/(:var)' => [
        'controller' => 'Admin:CarGallery@editAction'
    ],

    '/%s/module/rent-car/gallery/delete/(:var)' => [
        'controller' => 'Admin:CarGallery@deleteAction'
    ],

    '/%s/module/rent-car/gallery/save' => [
        'controller' => 'Admin:CarGallery@saveAction'
    ]
);
