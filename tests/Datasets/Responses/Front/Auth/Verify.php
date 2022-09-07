<?php

dataset('response.front.auth.verify.ok', [
    [
        // response
        [
            "user" => [
                "loghy_id" => "1",
                "user_id" => "111",
                "site_id" => "samplesite",
                "social_provider" => "line",
                "sub" => "string",
                "name" => "路義井太郎",
                "given_name" => "太郎",
                "given_name#ja-Kana-JP" => "タロウ",
                "given_name#ja-Hani-JP" => "太郎",
                "family_name" => "路義井",
                "family_name#ja-Kana-JP" => "ロギイ",
                "family_name#ja-Hani-JP" => "路義井",
                "middle_name" => "L",
                "nickname" => "ろぎー",
                "preferred_username" => "string",
                "profile" => "https =>//dummy.loghy.jp/profile",
                "picture" => "https =>//dummy.loghy.co.jp/picture.png",
                "website" => "https =>//dummy.loghy.jp/website",
                "email" => "loghy@example.com",
                "email_verified" => true,
                "gender" => "male",
                "birthdate" => "1986-01-01",
                "zoneinfo" => "Asia/Tokyo",
                "locale" => "ja-JP",
                "phone_number" => "+81 90-0000-0000",
                "phone_number_verified" => true,
                "address" => [
                    "formatted" => "string",
                    "street_address" => "string",
                    "locality" => "string",
                    "region" => "string",
                    "postal_code" => "string",
                    "country" => "string"
                ],
                "line_friend_flag" => true,
                "updated_at" => 1649082429,
                "location" => "string",
                "hometown" => "string",
                "description" => "string"
            ]
        ]
    ]
]);

dataset('response.front.auth.verify.error', [
    // $status, $responseJson
    'missing id_token' => [
        422,
        [
            "message" => "validation.required",
            "errors" => [
                "id_token" => [
                    "validation.required"
                ]
            ]
        ]
    ],
    'invalid id_token' => [
        422,
        [
            "message" => "Invalid ID token."
        ]
    ],
    'missing site_code' => [
        422,
        [
            "message" => "validation.required",
            "errors" => [
                "site_code" => [
                    "validation.required"
                ]
            ]
        ]
    ],
    'invalid site_code' => [
        422,
        [
            "message" => "Invalid site."
        ]
    ],
    'internal server error' => [
        500,
        [
            "message" => "Failed to verify ID token."
        ]
    ]
]);
