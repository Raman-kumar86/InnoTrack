<?php

return [
    'modules' => [
        'Funding' => 'trending-up',
        'Startups' => 'briefcase',
        'Users' => 'users',
        'Reports' => 'file-text',
        'State Analytics' => 'map',
        'Documents' => 'folder',
        'Incubators' => 'home',
        'Sectors' => 'layers',
        'System' => 'settings',
        'Notifications' => 'bell',
    ],
    'results' => [
        'Success' => [
            'label' => 'Success',
            'color' => 'green',
            'bg' => 'bg-green-100 dark:bg-green-950/40',
            'text' => 'text-green-700 dark:text-green-400',
            'dot' => 'bg-green-500',
        ],
        'Failed' => [
            'label' => 'Failed',
            'color' => 'rose',
            'bg' => 'bg-rose-100 dark:bg-rose-950/40',
            'text' => 'text-rose-700 dark:text-rose-400',
            'dot' => 'bg-rose-500',
        ],
        'Blocked' => [
            'label' => 'Blocked',
            'color' => 'amber',
            'bg' => 'bg-amber-100 dark:bg-amber-950/40',
            'text' => 'text-amber-700 dark:text-amber-400',
            'dot' => 'bg-amber-500',
        ],
        'Pending' => [
            'label' => 'Pending',
            'color' => 'blue',
            'bg' => 'bg-blue-100 dark:bg-blue-950/40',
            'text' => 'text-blue-700 dark:text-blue-400',
            'dot' => 'bg-blue-500',
        ],
    ],
    'date_ranges' => [
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        'this_week' => 'This week',
        'this_month' => 'This month',
        'custom' => 'Custom range',
    ],
    'per_page_options' => [25, 50, 100, 250],
    'prune_after_days' => 90,
];