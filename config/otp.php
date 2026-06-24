<?php

return [

    'expiry_minutes' => (int) env('OTP_EXPIRY_MINUTES', 10),

    'dev_mode' => filter_var(env('OTP_DEV_MODE', false), FILTER_VALIDATE_BOOLEAN),

    'dev_code' => env('OTP_DEV_CODE', '123456'),

];
