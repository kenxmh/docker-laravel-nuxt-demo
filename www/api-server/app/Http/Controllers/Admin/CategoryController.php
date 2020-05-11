<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Common\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * @group Admin-Category
     * 分类-列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = Category::all();
        return response()->json($list, 200);
    }

    /**
     * @group Admin-Category
     * 分类-创建
     *
     * @bodyParam name string required 分类名
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|min:2|max:32|unique:category',
            'key'   => 'required|string|min:2|max:32|unique:category',
            'color' => 'required|string',
        ]);
        $category        = new Category;
        $category->name  = $request->name;
        $category->key   = $request->key;
        $category->color = $request->color;
        $category->save();
        $category->refresh();
        return response()->json($category, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * @group Admin-Category
     * 分类-修改
     *
     * @bodyParam name string required 分类名
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|min:2|max:32',
            'key'   => 'required|string|min:2|max:32',
            'color' => 'required|string|min:2|max:6',
        ]);

        $category = Category::find($request->route('id'));
        if (empty($category->id)) {
            return response()->json([
                "error_code" => 1,
                "message"    => '数据不存在',
            ], 404);
        }

        // 重复的情况
        $has = Category::where('name', $request->name)->where('id', '!=', $request->id)->count();
        if ($has > 0) {
            return response()->json([
                "error_code" => 2,
                "message"    => '名字已使用',
            ], 406);
        }

        $has = Category::where('key', $request->key)->where('id', '!=', $request->id)->count();
        if ($has > 0) {
            return response()->json([
                "error_code" => 3,
                "message"    => 'key已使用',
            ], 406);
        }

        $category->name = $request->name;
        $category->key  = $request->key;
        $category->save();
        return response()->json([], 204);
    }

    /**
     * @group Admin-Category
     * 分类-删除
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if (empty($category->id)) {
            return response()->json([
                "error_code" => 1,
                "message"    => '数据不存在',
            ], 404);
        }

        DB::beginTransaction();
        try {
            $res1 = $category->articles()->detach();
            $res2 = $category->delete();
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
