<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\EmailNotVerifyException;
use Carbon\Carbon;

class CheckIPDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$scopes)
    {
        $user = null;
        
        try {
            $user = Auth::guard('sanctum')->user();
            // if($user->name == 'anonymous') {
            //     $date = Carbon::now()->subDays(1);
            //     $token = $user->currentAccessToken();
            //     $created_at = $token->created_at;
            //     $is_expired = $created_at->lt($date);
            //     if($is_expired) {
            //         $token->delete();
            //         $user = null;
            //     }
            // }
            // $token = $user->currentAccessToken();
            // $token->created_at = Carbon::now();
            // $token->save();
        } catch (\Exception $exception) {
            $user = null;
        }
        if ($user) {
            $is_email_verify = $user->hasVerifiedEmail();
            if(!$is_email_verify) {
                throw new EmailNotVerifyException('email_not_verify');
            }

            $can = false;
            foreach($scopes as $scope) {
                $can = $user->tokenCan($scope);
                if ($can) {
                    break;
                }
            }
            if ($can) {
                Auth::login($user);
                return $next($request);
            } else {
                throw new UnauthorizedException('unauthorized');
            }
        }
        $access = array_filter(array_map(function($v){
            return ( $star = strpos($v, "*") )
                    ? ( substr(getenv('REMOTE_ADDR'), 0, $star) == substr($v, 0, $star) )
                    : ( getenv('REMOTE_ADDR') == $v );
        }, config('custom.internal_ips')));
        
        if ($access)
        {
            return $next($request);
        }
        else
        {
            throw new AuthenticationException;
        }
    }
}
