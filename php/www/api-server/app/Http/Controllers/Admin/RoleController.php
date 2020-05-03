<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Access;
use App\Models\Admin\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * @group Admin-Role
     * 角色管理-列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $list = Role::leftJoin('admin_role_access', 'admin_role.id', '=', 'admin_role_access.role_id')
        // ->leftJoin('admin_access', 'admin_role_access.access_id', '=', 'admin_access.id')
        // ->select('admin_role.*', 'admin_access')
        // ->get()->toArray();

        $list = Role::all();
        return response()->json($list, 200);
    }

    /**
     * @group Admin-Role
     * 角色管理-创建
     *
     * @bodyParam name string required name
     * @bodyParam accesses array required 路由ID的数组 Example: [2,3]
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|min:2|max:255|unique:admin_role',
            'accesses' => 'array',
        ]);

        // 检查传入的accesses 里面ID是否存在
        $count = DB::table('admin_access')->whereIn('id', $request->accesses)->count();
        if ($count != count($request->accesses)) {
            return response()->json([
                'error_code' => 1,
                'message'    => '部分数据不存在',
            ], 404);
        }

        DB::beginTransaction();
        try {
            $role       = new Role;
            $role->name = $request->name;

            $re1 = $role->save();
            $re2 = $role->accesses()->sync($request->accesses);
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'error_code' => 2,
                'message'    => '未知错误，操作失败',
            ], 400);
        }

        return response()->json($role, 201);
    }

    /**
     * @group Admin-Role
     * 角色管理-详情
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
     * @group Admin-Role
     * 角色管理-更新
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'     => 'required|string|min:2|max:255',
            'accesses' => 'array',
        ]);

        $role = Role::find($request->route('id'));
        if (empty($role->id)) {
            return response()->json([
                "error_code" => 1,
                "message"    => '数据不存在',
            ], 404);
        }

        // 重复的情况
        $has = Role::where('name', $request->name)->where('id', '!=', $request->id)->count();
        if ($has > 0) {
            return response()->json([
                "error_code" => 2,
                "message"    => '该角色名已使用',
            ], 406);
        }

        // 检查传入的accesses 里面ID是否存在
        $count = DB::table('admin_access')->whereIn('id', $request->accesses)->count();
        if ($count != count($request->accesses)) {
            return response()->json([
                'error_code' => 3,
                'message'    => '部分数据不存在',
            ], 404);
        }

        DB::beginTransaction();
        try {
            $role->name = $request->name;

            $re1 = $role->save();
            $re2 = $role->accesses()->sync($request->accesses);
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'error_code' => 4,
                'message'    => '未知错误，操作失败',
            ], 400);
        }

        return response()->json([], 204);
    }

    /**
     * @group Admin-Role
     * 角色管理-删除
     *
     * @urlParam id required ID Example: 2
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::find($id);
        if (empty($role->id)) {
            return response()->json([
                "error_code" => 1,
                "message"    => '数据不存在',
            ], 404);
        }

        DB::beginTransaction();
        try {
            $res1 = $role->accesses()->detach();
            $res2 = $role->users()->detach();
            $res3 = $role->delete();
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

    /**
     * @group Admin-Role
     * 角色管理-角色的路由规则
     *
     * @urlParam id required ID Example: 2
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getAccesses($id)
    {
        $role = Role::find($id);
        if (empty($role->id)) {
            return response()->json([
                "error_code" => 1,
                "message"    => '数据不存在',
            ], 404);
        }

        $list = Access::leftJoin('admin_role_access', 'admin_access.id', '=', 'admin_role_access.access_id')
            ->where(['role_id' => $role->id])
            ->whereNotNull('admin_access.pid')
            ->select('admin_access.*')->get()->toArray();

        $finalArr = [];
        foreach ($list as $row) {
            if (empty($finalArr[$row['pid']])) {
                $finalArr[$row['id']]             = $row;
                $finalArr[$row['id']]['children'] = [];
            } else {
                // dump($finalArr[$row['group']]['children']);exit;
                array_push($finalArr[$row['pid']]['children'], $row);

            }
        }

        $finalArr = array_values($finalArr);
        return response()->json($list, 200);
    }
}
