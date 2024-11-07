<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    if($context['APP_ENV'] === 'dev') {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Credentials: true');
    }
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
