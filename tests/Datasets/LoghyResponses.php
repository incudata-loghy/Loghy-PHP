<?php

dataset('loghy_id_response', function () {
    yield fn() => [
        'result' => true,
        'data' => [
            'lgid' => 43686,
            'site_id' => '1',
            'social_login' => 'google',
            'sid' => 'exampleCode',
            'geturl' => "https://api001.sns-loghy.jp/api/lgid2get?cmd=lgid2get&sid=examplecode&id=43686&time=1638169393&rkey=00745824&skey=6f3f43700bb2fb5ba527b719f"
        ]
    ];
});

dataset('ok_response', function() {
    yield fn() => [
        'result' => true,
    ];
});
