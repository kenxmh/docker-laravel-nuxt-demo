<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class AdminMiddleware
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
        try {
            $user = auth('admin')->userOrFail();
            if ($user->status != 0) {

            }
            $actionArr = explode('\\', $request->route()->getActionName());
            $action    = implode("\\", [$actionArr[3], $actionArr[4]]);

            $accessObj = DB::table('admin_access')->select('id')->where(['action' => $action])->first();
            if (empty($accessObj->id)) {
                return response()->json([
                    'error_code' => 1,
                    'message'    => 'Access denial.',
                ], 403);
            }
            $hasPermission = DB::table('admin_role_access')->leftJoin('admin_user_role', 'admin_role_access.role_id', '=', 'admin_user_role.role_id')
                ->where([
                    'admin_user_role.admin_id'    => $user->id,
                    'admin_role_access.access_id' => $accessObj->id,
                ])->limit(1)->count();
            if (!$hasPermission) {
                return response()->json([
                    'error_code' => 2,
                    'message'    => 'Access denial.',
                ], 403);
            }
        } catch (UserNotDefinedException $e) {
            // 没有token
            return response()->json([
                'error_code' => 3,
                'message'    => 'User not found.',
            ], 403);
        }
        return $next($request);
    }
}
