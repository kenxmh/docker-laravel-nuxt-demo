<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Access;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccessController extends Controller
{
    /**
     * @group Admin-Access
     * 路由规则-列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list     = Access::all()->toArray();
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
        return response()->json($finalArr, 200);
    }

    /**
     * @group Admin-Access
     * 路由规则-创建
     *
     * @bodyParam name string required Name
     * @bodyParam action string required Action
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string',
            'action' => 'required|string',
        ]);

        $access         = new Access;
        $access->name   = $request->name;
        $access->action = $request->action;
        $access->save();
        return response()->json($access, 201);
    }

    /**
     * @group Admin-Access
     * 路由规则-详情
     *
     * @urlParam id required ID Example: 2
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(Access::find($id), 200);
    }

    /**
     * @group Admin-Access
     * 路由规则-更新
     *
     * @urlParam id required ID Example: 2
     * @bodyParam name string required Name
     * @bodyParam action string required Action
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'   => 'required|string',
            'action' => 'required|string',
        ]);
        $access = Access::find($request->route('id'));
        if (empty($access->id)) {
            return response()->json([
                "error_code" => 1,
                "message"    => '数据不存在',
            ], 404);
        }
        $access->name   = $request->name;
        $access->action = $request->action;
        $access->save();
        return response()->json([], 204);
    }

    /**
     * @group Admin-Access
     * 路由规则-删除
     *
     * @urlParam id required ID Example: 2
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $access = Access::find($id);
        if (empty($access->id)) {
            return response()->json([
                "error_code" => 1,
                "message"    => '数据不存在',
            ], 404);
        }

        DB::beginTransaction();
        try {
            $res1 = $access->accesses()->detach();
            $res2 = $access->delete();
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
