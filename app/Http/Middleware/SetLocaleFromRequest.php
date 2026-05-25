<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocaleFromRequest
{
    public function handle(Request $request, Closure $next)
    {
        $lang = $request->input('lang') ??$request->header('lang') ?? $request->header('Accept-Language') ?? 'en';
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';
        App::setLocale($lang);
        return $next($request);
    }
}
