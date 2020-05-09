<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Role;
use App\Models\Admin\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @group Admin-User
     * 后台用户-登录
     * @bodyParam username string required 用户名 Example: admin
     * @bodyParam password string required 密码 Example: 123456
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (!$token = auth()->guard('admin')->attempt($credentials)) {
            return response()->json([
                'error_code' => 1,
                'message'    => '用户名或密码不正确',
            ], 403);
        }

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60,
        ], 201);
    }

    /**
     * @group Admin-User
     * 后台用户-获取当前用户
     *
     * @return \Illuminate\Http\Response
     */
    public function user()
    {
        $user = auth()->guard('admin')->user()->toArray();

        $user['roles'] = Role::leftJoin('admin_user_role', 'admin_role.id', '=', 'admin_user_role.role_id')
            ->where(['admin_user_role.admin_id' => $user['id']])
            ->pluck('admin_role.name');
        return response()->json($user);
    }

    /**
     * @group Admin-User
     * 后台用户-修改个人信息
     *
     * @return \Illuminate\Http\Response
     */
    public function updateUser(Request $request)
    {
        $request->validate([
            'action' => 'required|string|in:profile,password',
        ]);
        $user = auth()->guard('admin')->user();
        if ($request->action == "profile") {
            $request->validate([
                'realname' => 'required|string|min:2',
            ]);
            $user->realname = $request->realname;
            $user->save();
        } else {
            $request->validate([
                'oldPassword' => 'required|string|min:6',
                'newPassword' => 'required|string|min:6',
                'rePassword'  => 'required|string|min:6|same:newPassword',
            ]);
            if (Hash::check($request->oldPassword, $user->password)) {
                $user->password = bcrypt($request->newPassword);
                $user->save();
            } else {
                return response()->json([
                    'error_code' => 1,
                    'message'    => '旧密码不正确',
                ], 403);
            }

        }

        return response()->json([], 204);
    }

    /**
     * @group Admin-User
     * 后台用户-登出当前用户
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        auth()->guard('admin')->logout();

        return response()->json([], 201);
    }

}
