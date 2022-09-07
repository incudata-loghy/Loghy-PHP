<?php

dataset('response.manage.user.bulk.delete.ok', [
    [
        // response
        [
            'message' => 'ok',
        ]
    ]
]);

dataset('response.manage.user.bulk.delete.error', [
    // $status, $responseJson
    'unauthenticated' => [
        401,
        [
            'message' => 'Unauthenticated.',
        ],
    ],
    'invalid ability' => [
        403,
        [
            'message' => 'Invalid ability provided.',
        ],
    ],
    'missing loghy_ids' => [
        422,
        [
            'message' => 'validation.required',
            'errors' => [
                [
                    'loghy_ids' => [
                        'validation.required',
                    ],
                ],
            ],
        ]
    ],
    'invalid loghy_id' => [
        422,
        [
            'message' => 'validation.exists',
            'errors' => [
                [
                    'loghy_ids' => [
                        [
                            'validation.exists',
                        ]
                    ],
                ],
            ],
        ]
    ]
]);
