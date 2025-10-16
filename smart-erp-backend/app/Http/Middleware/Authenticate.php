<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        // Untuk API request, jangan redirect
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }
        
        // Untuk web request
        return null;
    }
}