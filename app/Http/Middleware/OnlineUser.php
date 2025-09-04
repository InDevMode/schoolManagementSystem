<?php

namespace App\Http\Middleware;

use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;


class OnlineUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!empty(Auth::check())) {
            $expireTime = Carbon::now()->addMinutes(1);
            Cache::put('OnlineUser.' . Auth::user()->id, true, $expireTime);
            $getUserData = User::getSingle(Auth::user()->id);
            if (!empty($getUserData)) {
                $getUserData->update([
                    'last_login' => now(),
                ]);
            }
        }
        return $next($request);
    }
}
