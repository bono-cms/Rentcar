<?php

/**
 * Module configuration container
 */

return [
    'caption'  => 'Cars',
    'description' => 'Cars module allows you to manage cars booking on your site',
    // Bookmarks of this module
    'bookmarks' => [
        [
            'name' => 'View all cars',
            'controller' => 'Rentcar:Admin:Car@indexAction',
            'icon' => 'fas fa-car'
        ]
    ],

    'menu' => [
        'name'  => 'Cars',
        'icon' => 'fas fa-car',
        'items' => [
            [
                'route' => 'Rentcar:Admin:Car@indexAction',
                'name' => 'View all cars'
            ],

            [
                'route' => 'Rentcar:Admin:Lease@indexAction',
                'name' => 'Car leasing'
            ],

            [
                'route' => 'Rentcar:Admin:Booking@indexAction',
                'name' => 'Bookings'
            ],

            [
                'route' => 'Rentcar:Admin:Booking@availabilityAction',
                'name' => 'Availability graph'
            ],

            [
                'route' => 'Rentcar:Admin:Booking@statAction',
                'name' => 'Statistic'
            ]
        ]
    ]
];