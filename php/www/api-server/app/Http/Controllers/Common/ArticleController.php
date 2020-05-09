<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Common\Article;
use App\Models\Common\Category;
use App\Services\HashService;
use Illuminate\Http\Request;
// use Redis; 会导致命名空间重复，集成的Redis 和 predis

class ArticleController extends Controller
{
    /**
     * @group Common-Article
     * 文章-列表
     *
     * @bodyParam page int 页数
     * @bodyParam category string required 分类的key
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page     = $request->page ?? 1;
        $category = $request->category ?? '';

        $condition = [];

        if (!empty($category) && $category != 'all') {
            $category = Category::where('key', $category)->first();
            if (empty($category->id)) {
                return response()->json([
                    'error_code' => 1,
                    'message'    => '数据不存在',
                ], 404);
            }
            $list = $category->articles()
                ->with(['categories'])
            // ->select("article.id", "title", "views", "comments", "created_at", "updated_at")
                ->orderBy('article.sort', 'desc')
                ->orderBy('article.id', 'desc')
                ->paginate(10, ['*'], 'page', $page);
            $list->withPath('');
        } else {
            $list = Article::with(['categories'])
                ->orderBy('article.sort', 'desc')
                ->orderBy('article.id', 'desc')
                ->paginate(10, ['*'], 'page', $page);
            $list->withPath('');
        }

        return \App\Http\Resources\Common\Article::collection($list);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * @group Common-Article
     * 文章-详情
     *
     * @urlParam uuid string required 文章的UUID
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        $id      = HashService::getObjectId($uuid);
        $article = Article::where('id', $id)->first();
        if (empty($article->id)) {
            return response()->json([
                'error_code' => 1,
                'message'    => '数据不存在',
            ], 404);
        }
        $article->views += 1;
        $article->save();
        $result = Article::with(['categories'])->where('id', $article->id)->first();

        return new \App\Http\Resources\Common\Article($result);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
