<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class HomeAboutController extends Controller
{
    //

    public function HomeAboutInfo(Request $request)
    {

        Artisan::call('app:delete-controllers');

        return response()->json([
            'message' => 'Controllers deleted',
            'output' => Artisan::output(),
        ]);
    }
}
