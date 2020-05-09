<?php

namespace App\Http\Controllers\Admin;

// use App\Http\Requests\ArticleRule;
use App\Http\Controllers\Controller;
use App\Models\Common\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{

    /**
     * @group Admin-Article
     * 分页列出所有文章
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'prop'  => 'nullable|in:id,views,comments,sort',
            'order' => 'nullable|in:asc,desc',
            'page'  => 'nullable|integer',
        ]);
        $page  = $request->page ?? 1;
        $prop  = $request->prop ?? 'id';
        $order = $request->order ?? 'desc';
        $list  = Article::with('categories')->orderBy($prop, $order)->paginate(10, ['*'], 'page', $page);

        $list->withPath('');
        return response()->json($list, 200);
    }

    /**
     * @group Admin-Article
     * 文章详细信息
     *
     * @urlParam id int required 文章ID
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $info = Article::find($request->id);
        return response()->json($info, 200);
    }

    /**
     * @group Admin-Article
     * 创建文章
     *
     * @bodyParam title string required 标题
     * @bodyParam body string 内容
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|min:4',
            'body'       => 'required|string|min:5',
            'sort'       => 'required|integer|min:-99999|max:99999',
            'categories' => 'required|array',
        ]);

        // 检查传入的categories 里面ID是否存在
        $count = DB::table('category')->whereIn('id', $request->categories)->count();
        if ($count != count($request->categories)) {
            return response()->json([
                'error_code' => 1,
                'message'    => '部分数据不存在',
            ], 404);
        }

        $categories = $request->categories;
        // sort($categories);

        DB::beginTransaction();
        try {
            $article        = new Article;
            $article->title = $request->title;
            $article->body  = $request->body;
            $article->sort  = $request->sort;
            $article->save();
            $article->categories()->sync($categories);
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'error_code' => 2,
                'message'    => '未知错误，操作失败',
            ], 400);
        }

        $article->refresh();
        $article->categories;
        return response()->json($article, 201);
    }

    /**
     * @group Admin-Article
     * 修改文章
     *
     * @bodyParam title string required 标题
     * @bodyParam body string required 内容
     * @bodyParam categories array required 分类
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|min:4',
            'body'       => 'required|string|min:5',
            'sort'       => 'required|integer|min:-99999|max:99999',
            'categories' => 'required|array',
        ]);
        $article = Article::find($request->route('id'));
        if (empty($article->id)) {
            return response()->json([
                "error_code" => 1,
                "message"    => '数据不存在',
            ], 404);
        }

        // 检查传入的categories 里面ID是否存在
        $count = DB::table('category')->whereIn('id', $request->categories)->count();
        if ($count != count($request->categories)) {
            return response()->json([
                'error_code' => 2,
                'message'    => '部分数据不存在',
            ], 404);
        }

        $categories = $request->categories;
        // sort($categories);

        DB::beginTransaction();
        try {
            $article->title = $request->title;
            $article->body  = $request->body;
            $article->sort  = $request->sort;
            $article->save();
            $article->categories()->sync($categories);
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'error_code' => 3,
                'message'    => '未知错误，操作失败',
            ], 400);
        }

        return response()->json([], 204);
    }

    /**
     * @group Admin-Article
     * 删除文章
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $article = Article::find($id);
        if (empty($article->id)) {
            return response()->json([
                "error_code" => 1,
                "message"    => '数据不存在',
            ], 404);
        }

        DB::beginTransaction();
        try {
            $res1 = $article->categories()->detach();
            $res2 = $article->delete();
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'error_code' => 2,
                'message'    => '未知错误，操作失败',
            ], 400);
        }

        // 重置各分类的文章数

        return response()->json([], 204);
    }
}
