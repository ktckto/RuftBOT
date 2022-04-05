<?php
require ('class/load.php');
$config=[
    'main'=>[
        'token'=>'1178883535:AAF-UVN5lu1ubzcVbWWKPvXv60mjQ1EIyFY'
    ],
    'hcaptcha'=>[
        'public'=>'064e99dc-67ad-4f8b-97cc-69d163d2e8c0',
        'secret'=>'0xd02a7e9CD3EA2e45CAd97b909AA6A28672394d6d'
    ]
];

function checkCaptcha($SECRET_KEY){
$data = array(
    'secret' => $SECRET_KEY,
    'response' => $_POST['h-captcha-response']
);
$verify = curl_init();
curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
curl_setopt($verify, CURLOPT_POST, true);
curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($verify);
// var_dump($response);
$responseData = json_decode($response);
if($responseData->success) {
    return true;
}
else {
    return false;
}
}