<?php

use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    App\Modules\Projects\ServiceProvider::class,
    App\Modules\Tasks\ServiceProvider::class,
];
