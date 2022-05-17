<?php

dataset('loghy_id_response', [
    [[
        'result' => true,
        'data' => [
            'lgid' => 43686,
            'site_id' => '1',
            'social_login' => 'google',
            'sid' => 'exampleCode',
            'geturl' => "https://api001.sns-loghy.jp/api/lgid2get?cmd=lgid2get&sid=examplecode&id=43686&time=1638169393&rkey=00745824&skey=6f3f43700bb2fb5ba527b719f"
        ]
    ]]
]);

dataset('invalid_code_response', [
    'invalid_code' => [['result' => false, 'error_code' => 211, 'error_message' => 'The code passed is invalid.']],
    'expired_code' => [['result' => false, 'error_code' => 211, 'error_message' => 'The authentication code is expired.']],
    'missing_code' => [['result' => false, 'error_code' => 211, 'error_message' => 'Missing code parameter error']],
]);

dataset('unexpected_response', [
    'wrong_structure' => [['wrong_key' => 'wrong_value']],
    'without_data' => [['result' => true]],
    'without_lgid' => [['result' => true, 'data' => []]],
]);

dataset('ok_response', function () {
    yield fn () => [
        'result' => true,
    ];
});
