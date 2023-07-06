<?php

namespace App\Http\Controllers;

use Artisan;

class ResetController extends Controller
{
    public function __invoke()
    {
        Artisan::call('migrate:refresh');

        return 'OK';
    }
}
