<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AccessLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $repository_accesslog = app()->make(\App\Repositories\Log\AccessLogRepositoryInterface::class);
        $access = array();
        $user = auth_user();
        if ($user)
        {
            $access['user_id'] = $user->id;
            $token = $user->currentAccessToken();
            $access['token_id'] = $token->id;
        }
        $access['access_ip'] = $request->ip();
        $access['method'] = $request->method();
        $access['access_url'] = $request->path();
        $query_str = substr($request->getQueryString(), 0, 255);
        $access['query_str'] = isset($query_str) ? $query_str : '';
        
        $repository_accesslog->LogAccess($access);
        return $next($request);
    }
}
