<?php

dataset('response.front.user.social_provider_token.ok', [
    [
        // response
        [
            [
                'provider' => 'apple',
                'token' => '...',
                'token_secret' => NULL,
                'refresh_token' => '...',
                'expires_in' => 3600,
            ],
            [
                'provider' => 'line',
                'token' => '...',
                'token_secret' => NULL,
                'refresh_token' => '...',
                'expires_in' => 3600,
            ],
        ]
    ]
]);

dataset('response.front.user.social_provider_token.error', [
    // $status, $json
    [
        401,
        [
            'message' => 'Unauthenticated.',
        ]
    ],
    [
        403,
        [
            'message' => 'Invalid ability provided.',
        ]
    ],
]);
