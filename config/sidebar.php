<?php

define('IS_ADMIN', 1);
define('IS_MEMBER', 0);

return [
    'menu' => [
        [
            'icon'  => 'fa-home',
            'title' => 'Dashboard',
            'url'   => 'dashboard',
            'akses' => [IS_ADMIN, IS_MEMBER]
        ],
        [
            'icon'     => 'fa-user-secret',
            'title'    => 'Participants',
            'url'      => 'user',
            'akses' => [IS_ADMIN],
        ],
        [
            'icon'     => 'fa-copy',
            'title'    => 'Abstracts',
            'url'      => 'abstracts',
            'akses' => [IS_ADMIN],
        ],
        [
            'icon'     => 'fa-book',
            'title'    => 'Full Paper',
            'url'      => 'fullpapers',
            'akses' => [IS_ADMIN],
        ],
        [
            'icon'     => 'fa-bullhorn',
            'title'    => 'Announcements',
            'url'      => 'announcement',
            'akses' => [IS_ADMIN],
        ],[
            'icon'  => 'fa-user',
            'title' => 'Personal Details',
            'url'   => 'personal',
            'akses' => [IS_MEMBER]
        ], [
            'icon'  => 'fa-cloud',
            'title' => 'Abstract Submission',
            'url'   => 'abstract',
            'akses' => [IS_MEMBER]
        ], [
            'icon'  => 'fa-cloud-upload',
            'title' => 'Full Paper Submission',
            'url'   => 'fullpaper',
            'akses' => [IS_MEMBER],
        ], [
            'icon'  => 'fa-cc-mastercard',
            'title' => 'Payment',
            'url'   => 'payment',
            'akses' => [IS_MEMBER, IS_ADMIN]
        ],
        [
            'icon'  => 'fa-credit-card',
            'title' => 'Invoices',
            'url'   => 'invoice',
            'akses' => [IS_MEMBER]
        ],
        [
            'icon'  => 'fa-comments-o',
            'title' => 'Invoice Notification',
            'url'   => 'invoice-notification',
            'akses' => [IS_ADMIN]
        ], [
            'icon'  => 'fa-credit-card',
            'title' => 'Payment Notification',
            'url'   => 'payment-notification',
            'akses' => [IS_ADMIN, IS_MEMBER]
        ],
        [
            'icon'  => 'fa-cogs',
            'title' => 'Setting',
            'url'   => 'setting',
            'akses' => [IS_ADMIN]
        ]
    ]
];
