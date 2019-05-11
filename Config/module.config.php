<?php

/**
 * Module configuration container
 */

return array(
    'caption'  => 'Cars',
    'description' => 'Cars module allows you to manage cars booking on your site',
    // Bookmarks of this module
    'bookmarks' => array(
        array(
            'name' => 'View all cars',
            'controller' => 'Rentcar:Admin:Car@indexAction',
            'icon' => 'fas fa-car'
        )
    ),
    'menu' => array(
        'name'  => 'Cars',
        'icon' => 'fas fa-car',
        'items' => array(
            array(
                'route' => 'Rentcar:Admin:Car@indexAction',
                'name' => 'View all cars'
            ),
            array(
                'route' => 'Rentcar:Admin:Lease@indexAction',
                'name' => 'Car leasing'
            )
        )
    )
);