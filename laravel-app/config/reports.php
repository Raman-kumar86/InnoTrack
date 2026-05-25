<?php

return [
    'fiscal_years' => [
        'FY2324' => [
            'label' => 'FY 2023-24',
            'start' => '2023-04-01',
            'end' => '2024-03-31',
        ],
        'FY2223' => [
            'label' => 'FY 2022-23',
            'start' => '2022-04-01',
            'end' => '2023-03-31',
        ],
        'FY2122' => [
            'label' => 'FY 2021-22',
            'start' => '2021-04-01',
            'end' => '2022-03-31',
        ],
        'FY2021' => [
            'label' => 'FY 2020-21',
            'start' => '2020-04-01',
            'end' => '2021-03-31',
        ],
    ],

    'usd_to_inr' => 83.5,

    'export_formats' => [
        'executive_summary' => [
            'label' => 'Executive Summary',
            'hint' => 'PDF, print-ready',
            'icon' => 'file-text',
            'route' => 'reports.export.executive',
            'format' => 'pdf',
        ],
        'funding_tracker' => [
            'label' => 'Funding Tracker',
            'hint' => 'CSV, XLSX',
            'icon' => 'trending-up',
            'route' => 'reports.export.funding',
            'format' => 'csv',
        ],
        'state_analytics' => [
            'label' => 'State Analytics Pack',
            'hint' => 'PDF, chart bundle',
            'icon' => 'map',
            'route' => 'reports.export.states',
            'format' => 'pdf',
        ],
    ],
];