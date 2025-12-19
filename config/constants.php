<?php
$app_config = ['SUB_MENU_COLUMNS' => '2','LOCATION_TRACK_TIME'=>'10','INDIAN_RUPEE_SYMBOL'=>'₹'];
$order_config= ['ORDER_NUMBER_LENGTH' => 6,'ORDER_NUMBER_ALLOW_LEADING_ZEROS' => true];
$token_config = ['TOKEN_EXPIRY_TIME'=>90,'TOKEN_EXPIRY_UNIT'=>'days'];
return array_merge([
    'CUSTOM_SECRET_KEY' => "evCGZq2Ypx6Axw2UFKxGojV8A8zhhbK3mEq0A93j0Qk=",
    'BASE_URL' => "https://trudataa.revateam.com/"
], $app_config,$order_config,$token_config);
