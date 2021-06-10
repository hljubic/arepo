<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Zimbra\Admin\AdminFactory;

class ZimbraAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
//        $user = $request->user();
        $user = User::first();
        $api = AdminFactory::instance(env('ZIMBRA_ADMIN_URL'));
        $seconds = 39600; // 11 sati

        $token = Cache::remember('zimbra-token-' . $user->id, $seconds, function () use ($user, $api) {
            $tokenResponse = $api->auth(env('ZIMBRA_ADMIN_NAME'), env('ZIMBRA_ADMIN_PASSWORD'));
            return $tokenResponse->authToken;
        });
        $api->getClient()->setAuthToken($token);

        return $next($request);
    }
}
