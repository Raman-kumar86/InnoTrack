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
    ],

    'quarters' => [
        'ALL' => [
            'label' => 'Full Year',
            'month_start' => 4,
            'month_end' => 3,
        ],
        'Q1' => [
            'label' => 'Q1 (Apr-Jun)',
            'month_start' => 4,
            'month_end' => 6,
        ],
        'Q2' => [
            'label' => 'Q2 (Jul-Sep)',
            'month_start' => 7,
            'month_end' => 9,
        ],
        'Q3' => [
            'label' => 'Q3 (Oct-Dec)',
            'month_start' => 10,
            'month_end' => 12,
        ],
        'Q4' => [
            'label' => 'Q4 (Jan-Mar)',
            'month_start' => 1,
            'month_end' => 3,
        ],
    ],

    'usd_to_inr' => 83.5,

    'high_growth_threshold' => 50,

    'tier_thresholds' => [
        'tier1' => 25,
        'tier2' => 15,
    ],
];
