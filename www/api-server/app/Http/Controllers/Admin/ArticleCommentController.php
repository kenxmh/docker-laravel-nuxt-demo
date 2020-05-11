<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Common\Article;
use App\Models\Common\ArticleComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'prop'      => 'nullable|in:id,views,comments,sort',
            'order'     => 'nullable|in:asc,desc',
            'page'      => 'nullable|integer',
            'articleId' => 'nullable|integer',
        ]);
        $page  = $request->page ?? 1;
        $prop  = $request->prop ?? 'id';
        $order = $request->order ?? 'desc';

        $model = new ArticleComment();
        if ($request->articleId) {
            $model = $model->where('article_id', $request->articleId);
        }
        $list = $model->orderBy($prop, $order)->paginate(10, ['*'], 'page', $page);

        $list->withPath('');
        return response()->json($list, 200);
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
        $request->validate([
            'article_id' => 'required|integer',
            'quote_id'   => 'required|integer',
            'content'    => 'required|string|min:5',
        ]);

        $article = Article::where('id', $request->article_id)->first();
        if (empty($article->id)) {
            return response()->json([
                'error_code' => 1,
                'message'    => '数据不存在',
            ], 404);
        }

        if ($request->quote_id) {
            $articleComment = ArticleComment::where('id', $request->quote_id)->where('is_show', 1)->first();
            if (empty($articleComment->id)) {
                return response()->json([
                    'error_code' => 2,
                    'message'    => '数据不存在',
                ], 404);
            }
        }
        DB::beginTransaction();
        try {
            $comment             = new ArticleComment;
            $comment->article_id = $article->id;
            $comment->quote_id   = $articleComment->id ?? 0;
            $comment->nickname   = '';
            $comment->is_author   = 1;
            $comment->content = htmlspecialchars($request->content);
            $comment->ip      = $request->ip();
            $comment->save();

            $article->comments += 1;
            $article->save();
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'error_code' => 3,
                'message'    => '未知错误，操作失败',
            ], 400);
        }

        $comment->refresh();
        return response()->json($comment, 200);
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
