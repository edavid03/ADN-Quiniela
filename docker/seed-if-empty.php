<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

if (filter_var(env('AUTO_SEED', true), FILTER_VALIDATE_BOOL)
    && Schema::hasTable('equipos')
    && DB::table('equipos')->doesntExist()) {
    Artisan::call('db:seed', ['--force' => true]);
    echo Artisan::output();
}
