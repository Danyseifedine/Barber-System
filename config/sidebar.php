<?php

return [
    'menu_items' => [
        // Dashboard
        [
            'icon' => 'bi bi-grid fs-2',
            'title' => 'Dashboard',
            'route_in' => 'dashboard.index',
            'submenu' => [
                [
                    'title' => 'Default',
                    'link' => 'dashboard.index',
                    'is_route' => true,
                    'icon' => 'bi bi-house fs-2'
                ]
            ]
        ],

        // Section Title
        [
            'is_heading' => true,
            'title' => 'Pages',
        ],

        // User Profile Menu
        [
            'icon' => 'bi bi-person-lines-fill fs-2',
            'title' => 'Customers',
            'route_in' => 'dashboard.users.*',
            'submenu' => [
                [
                    'title' => 'Overview',
                    'link' => 'dashboard.users.index',
                    'is_route' => true,
                    'icon' => 'bi bi-person fs-2'
                ],
            ]
        ],

        [
            'icon' => 'bi bi-calendar-check fs-2',
            'title' => 'Business Hours',
            'route_in' => 'dashboard.businessHours.*',
            'submenu' => [
                [
                    'title' => 'Overview',
                    'link' => 'dashboard.businessHours.index',
                    'is_route' => true,
                    'icon' => 'bi bi-clock-history fs-2'
                ]
            ]
        ],

        [
            'icon' => 'bi bi-scissors fs-2',
            'title' => 'Services',
            'route_in' => 'dashboard.services.*',
            'submenu' => [
                [
                    'title' => 'Overview',
                    'link' => 'dashboard.services.index',
                    'is_route' => true,
                    'icon' => 'bi bi-paperclip fs-2'
                ]
            ]
        ],

        [
            'icon' => 'bi bi-calendar fs-2',
            'title' => 'Appointments',
            'route_in' => 'dashboard.appointments.*',
            'submenu' => [
                [
                    'title' => 'Overview',
                    'link' => 'dashboard.appointments.index',
                    'is_route' => true,
                    'icon' => 'bi bi-alarm fs-2'
                ],
                [
                    'title' => 'Services',
                    'link' => 'dashboard.appointments.services.index',
                    'is_route' => true,
                    'icon' => 'bi bi-arrows-move fs-2'
                ]
            ]
        ],

        [
            'icon' => 'bi bi-cash-coin fs-2',
            'title' => 'Payments',
            'route_in' => 'dashboard.payments.*',
            'submenu' => [
                [
                    'title' => 'Overview',
                    'link' => 'dashboard.payments.index',
                    'is_route' => true,
                    'icon' => 'bi bi-cash-coin fs-2'
                ]
            ]
        ],

        [
            'icon' => 'bi bi-envelope fs-2',
            'title' => 'Feedback',
            'route_in' => 'dashboard.feedback.*',
            'submenu' => [
                [
                    'title' => 'Overview',
                    'link' => 'dashboard.feedbacks.index',
                    'is_route' => true,
                    'icon' => 'bi bi-envelope fs-2'
                ]
            ]
        ],

        [
            'is_heading' => true,
            'title' => 'Privileges',
        ],

        // privileges
        [
            'icon' => 'bi bi-lock fs-2',
            'title' => 'Roles',
            'route_in' => 'dashboard.roles.*',
            'submenu' => [
                [
                    'title' => 'Overview',
                    'link' => 'dashboard.roles.index',
                    'is_route' => true,
                    'icon' => 'bi bi-body-text fs-2'
                ],
                [
                    'title' => 'User Role',
                    'link' => 'dashboard.roles.users.index',
                    'is_route' => true,
                    'icon' => 'bi bi-person-lock fs-2'
                ]
            ]
        ],
        // permissions
        [
            'icon' => 'bi bi-shield-check fs-2',
            'title' => 'Permissions',
            'route_in' => 'dashboard.permissions.*',
            'submenu' => [
                [
                    'title' => 'Overview',
                    'link' => 'dashboard.permissions.index',
                    'is_route' => true,
                    'icon' => 'bi bi-body-text fs-2'
                ],
                [
                    'title' => 'User Permissions',
                    'link' => 'dashboard.permissions.users.index',
                    'is_route' => true,
                    'icon' => 'bi bi-person-lock fs-2'
                ]
            ]
        ]
    ]
];
