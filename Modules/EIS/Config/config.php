<?php

use FontLib\Table\Type\name;

return [

    'name' => 'EIS',
    
    'enabled' => true,

    'environment' => env('EIS_ENV', 'sandbox'),

    'base_url' => env('EIS_BASE_URL'),

    'timeout' => 30,

    'retry' => 3,

];
