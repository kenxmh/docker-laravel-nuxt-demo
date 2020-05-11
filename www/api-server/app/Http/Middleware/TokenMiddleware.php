<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class TokenMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role = null)
    {
        try {
            $this->checkForToken($request);

            $token = $this->auth->parseToken();
            if ($this->auth->parseToken()->getClaim('role') != $role) {
                return response()->json([
                    'error_code' => 1,
                    'message'    => 'Token unmatched, please check.',
                ], 401);
            }

            return $next($request);

        } catch (TokenExpiredException $e) {
            try {
                // 尝试刷新用户的 token
                $token = $this->auth->refresh();
                // 使用一次性登录以保证此次请求的成功
                Auth::guard($role)->onceUsingId($this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub']);
            } catch (JWTException $exception) {
                // refresh 也过期，重新登录
                return response()->json([
                    'error_code' => 2,
                    'message'    => 'Token invalid, please re-login.',
                ], 401);
            }
        } catch (TokenInvalidException $e) {
            // token格式错误，非本系统生成的token
            return response()->json([
                'error_code' => 3,
                'message'    => 'Token invalid, please re-login.',
            ], 401);
        } catch (UnauthorizedHttpException $e) {
            // 没有token
            return response()->json([
                'error_code' => 3,
                'message'    => 'Token is required.',
            ], 401);
        }

        return $this->setAuthenticationHeader($next($request), $token);
    }
}
