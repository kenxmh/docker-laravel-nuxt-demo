<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Role;
use App\Models\Admin\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * @group Admin-User
     * 后台用户-列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = User::leftJoin('admin_user_role', 'admin_user.id', '=', 'admin_user_role.admin_id')
            ->leftJoin('admin_role', 'admin_role.id', '=', 'admin_user_role.role_id')
            ->select('admin_user.*', 'admin_role.id as role_id', 'admin_role.name as role_name')
            ->orderBy('admin_user.id')
            ->get();
        // $list->withPath('');
        return response()->json($list, 200);
    }

    /**
     * @group Admin-User
     * 后台用户-创建
     *
     * @bodyParam username string required 用户名 Example: regtest
     * @bodyParam password string required 密码 Example: 123456
     * @bodyParam email string required 邮箱 Example: regtest@kenbucket.com
     * @bodyParam realname string 真实姓名
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:3|max:255|unique:admin_user',
            'realname' => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:admin_user',
            'password' => 'required|string|min:6',
        ]);

        $role = Role::find($request->role_id);
        if (empty($role->id)) {
            return response()->json([
                'error_code' => 1,
                'message'    => '数据不存在',
            ], 404);
        }

        DB::beginTransaction();
        try {
            $user           = new User;
            $user->username = $request->username;
            $user->password = bcrypt($request->password);
            $user->email    = $request->email;
            $user->realname = $request->realname;

            $re1 = $user->save();
            $re2 = $user->roles()->sync($request->role_id);
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'error_code' => 2,
                // 'message'    => '未知错误，操作失败',
                'message'    => $e->getMessage(),
            ], 400);
        }

        $user->role_id   = $role->id;
        $user->role_name = $role->name;
        return response()->json($user, 201);
    }

    /**
     * @group Admin-User
     * 后台用户-详情
     *
     * @urlParam id required ID Example: 2
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(User::find($id), 200);
    }

    /**
     * @group Admin-User
     * 后台用户-更新
     *
     * @urlParam id required ID Example: 2
     * @bodyParam email string required 邮箱 Example: regtest@kenbucket.com
     * @bodyParam realname string 真实姓名
     * @bodyParam status int 状态 Example: 0
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'realname' => 'required|string|max:255',
            'email'    => 'required|string|email|max:255',
        ]);
        $user = User::find($request->route('id'));
        if (empty($user->id)) {
            return response()->json([
                "error_code" => 1,
                "message"    => '数据不存在',
            ], 404);
        }

        // 邮箱 重复的情况
        $has = User::where('email', $request->email)->where('id', '!=', $request->id)->count();
        if ($has > 0) {
            return response()->json([
                "error_code" => 2,
                "message"    => '该邮箱已使用',
            ], 406);
        }

        $user->email    = $request->email;
        $user->realname = $request->realname;
        $user->status   = $request->status;
        $user->save();
        return response()->json([], 204);
    }

    /**
     * @group Admin-User
     * 后台用户-删除
     *
     * @urlParam id required ID Example: 2
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (empty($user->id)) {
            return response()->json([
                "error_code" => 1,
                "message"    => '数据不存在',
            ], 404);
        }

        DB::beginTransaction();
        try {
            $user->rules()->detach();
            $user->delete();
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'error_code' => 2,
                'message'    => '未知错误，操作失败',
            ], 400);
        }
        return response()->json($result, 204);
    }

    /**
     * @group Admin-User
     * 后台用户-重置密码
     *
     * @urlParam id required ID Example: 2
     * @bodyParam password string required 密码
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6',
        ]);
        $user = User::find($request->route('id'));
        if (empty($user->id)) {
            return response()->json([
                "error_code" => 1,
                "message"    => '数据不存在',
            ], 404);
        }

        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json([], 204);
    }

    /**
     * @group Admin-User
     * 后台用户-修改角色
     *
     * @urlParam id required ID Example: 2
     * @bodyParam role_id int required 角色ID Example: 2
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function resetRole(Request $request)
    {
        $request->validate([
            'role_id' => 'required|integer',
        ]);
        $user = User::find($request->id);
        if (empty($user->id)) {
            return response()->json([
                "error_code" => 1,
                "message"    => '数据不存在',
            ], 404);
        }

        DB::beginTransaction();
        try {
            $user->status = User::STATUS_RELOGIN;
            $user->save();
            $user->roles()->sync($request->role_id);
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'error_code' => 2,
                'message'    => '未知错误，操作失败',
            ], 400);
        }

        return response()->json([], 204);
    }
}
