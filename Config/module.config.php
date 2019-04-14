<?php

/**
 * Module configuration container
 */

return array(
    'caption'  => 'Cars',
    'description' => 'Cars module allows you to manage cars booking on your site',
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