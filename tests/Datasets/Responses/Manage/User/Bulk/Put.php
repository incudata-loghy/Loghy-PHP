<?php

dataset('response.manage.user.bulk.put.ok', [
    [
        // response
        [
            'message' => 'ok',
        ]
    ]
]);

dataset('response.manage.user.bulk.put.error', [
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
    'missing users' => [
        422,
        [
            'message' => 'validation.required',
            'errors' => [
                'users' => [
                    'validation.required',
                ],
            ],
        ]
    ],
    'missing loghy_id' => [
        422,
        [
            'message' => 'validation.required',
            'errors' => [
                'users' => [
                    [
                        'loghy_id' => [
                            'validation.required',
                        ],
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
                'users' => [
                    [
                        'loghy_id' => [
                            'validation.exists',
                        ],
                    ],
                ],
            ],
        ]
    ],
    'missing user_id' => [
        422,
        [
            'message' => 'validation.exists',
            'errors' => [
                'users' => [
                    [
                        'settings' => [
                            'user_id' => [
                                'validation.required',
                            ],
                        ],
                    ],
                ],
            ],
        ]
    ]
]);
