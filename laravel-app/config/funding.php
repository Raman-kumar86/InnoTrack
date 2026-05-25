<?php

return [
    'round_types' => [
        'Pre-Seed',
        'Seed',
        'Angel',
        'Series A',
        'Series B',
        'Series C',
        'Bridge',
        'Convertible Note',
        'Grant',
        'Debt',
    ],

    'currencies' => [
        'USD' => ['label' => 'USD - US Dollar', 'symbol' => '$'],
        'INR' => ['label' => 'INR - Indian Rupee', 'symbol' => 'Rs.'],
        'EUR' => ['label' => 'EUR - Euro', 'symbol' => 'EUR'],
        'GBP' => ['label' => 'GBP - British Pound', 'symbol' => 'GBP'],
    ],

    'investor_types' => [
        'Angel',
        'VC',
        'PE',
        'Government',
        'Corporate',
        'Family Office',
        'Accelerator',
        'Incubator',
    ],

    'round_statuses' => [
        'Completed',
        'Pending',
        'In Progress',
    ],

    'exchange_rates' => [
        'INR' => 83.5,
        'EUR' => 0.92,
        'GBP' => 0.79,
        'USD' => 1.0,
    ],
];
