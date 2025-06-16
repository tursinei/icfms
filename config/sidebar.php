<?php

[$ALL, $ADMIN_ACCESS, $MEMBER_ACCESS] = [[
    config('app.roles.admin'),
    config('app.roles.member'),
],[config('app.roles.admin')],
[config('app.roles.member')]
];
return [
    'menu' => [
        [
            'icon'  => 'fa-home',
            'title' => 'Dashboard',
            'url'   => 'dashboard',
            'akses' => $ALL
        ],
        [
            'icon'     => 'fa-user-secret',
            'title'    => 'Participants',
            'url'      => 'user',
            'akses' => $ADMIN_ACCESS,
        ],
        [
            'icon'     => 'fa-copy',
            'title'    => 'Abstracts',
            'url'      => 'abstracts',
            'akses' => $ADMIN_ACCESS,
        ],
        [
            'icon'     => 'fa-book',
            'title'    => 'Full Paper',
            'url'      => 'fullpapers',
            'akses' => $ADMIN_ACCESS,
        ],
        [
            'icon'     => 'fa-bullhorn',
            'title'    => 'Announcements',
            'url'      => 'announcement',
            'akses' => $ADMIN_ACCESS,
        ], [
            'icon'  => 'fa-user',
            'title' => 'Personal Details',
            'url'   => 'personal',
            'akses' => $MEMBER_ACCESS
        ], [
            'icon'  => 'fa-cloud',
            'title' => 'Abstract Submission',
            'url'   => 'abstract',
            'akses' => $MEMBER_ACCESS
        ], [
            'icon'  => 'fa-cloud-upload',
            'title' => 'Full Paper Submission',
            'url'   => 'fullpaper',
            'akses' => $MEMBER_ACCESS,
        ], [
            'icon'  => 'fa-cc-mastercard',
            'title' => 'Payment via Bank Transfer',
            'url'   => 'payment',
            'akses' => $ALL
        ],
        [
            'icon'  => 'fa-credit-card',
            'title' => 'Invoices',
            'url'   => 'invoice',
            'akses' => $MEMBER_ACCESS,
        ],
        [
            'icon'  => 'fa-comments-o',
            'title' => 'Invoice Notification',
            'url'   => '#',
            'akses' => $ADMIN_ACCESS,
            'sub_menu' => [
                [
                    'icon'  => 'fa-suitcase',
                    'title' => 'Registration',
                    'url'   => 'invoice-notification',
                    'akses' => $ADMIN_ACCESS
                ],
                [
                    'icon'  => 'fa-hotel',
                    'title' => 'Hotel',
                    'url'   => 'invoice-hotel',
                    'akses' => $ADMIN_ACCESS
                ],
            ]
        ], [
            'icon'  => 'fa-credit-card',
            'title' => 'Payment Notification',
            'url'   => 'payment-notification',
            'akses' => $ALL
        ],
        [
            'icon'  => 'fa-file-text-o',
            'title' => 'Documents',
            'url'   => 'documents',
            'akses' => $ALL
        ],
        [
            'icon'  => 'fa-cogs',
            'title' => 'Setting',
            'url'   => 'setting',
            'akses' => $ADMIN_ACCESS
        ]
    ]
];
