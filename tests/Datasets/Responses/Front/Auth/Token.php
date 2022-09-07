<?php

dataset('response.front.auth.token.ok', [
    [
        // response
        [
            "access_token" => "88|7QaagoNLTCn4KHQiRaz4qm0d8bGvIOV2fUTSNrrS",
            "expires_in" => 600,
            "id_token" => "xxxxxxxxxx.xxxxxxxxx.xxxxxxxx",
            "scope" => "read-end_user",
            "token_type" => "Bearer",
        ]
    ]
]);

dataset('response.front.auth.token.error', [
    // $status, $json
    [
        422,
        [
            'message' => "Invalid authorization code.",
        ]
    ]
]);
