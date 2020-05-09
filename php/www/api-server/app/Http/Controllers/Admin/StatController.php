<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Common\ArticleComment;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class StatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // v-charts 图表格式
        $result = DB::select("
            (
                SELECT
                    'day' type,
                    count(id) total,
                    DATE_FORMAT(created_at, '%Y-%m-%d') `period`
                FROM
                    `article_comment`
                where
                    `created_at` > :start_time1
                GROUP BY
                    `period`
            )
            UNION ALL
            (
                SELECT
                    'week' type,
                    count(id) total,
                    DATE_FORMAT(created_at, '%Y-%u') `period`
                FROM
                    `article_comment`
                where
                    `created_at` > :start_time2
                GROUP BY
                    `period`
            )
        ", [
            ':start_time1' => date("Y-m-d 00:00:00", strtotime("-1 week")),
            ':start_time2' => date("Y-m-d 00:00:00", strtotime("-7 week"))
        ]);

        $commentStats = [
            'day' => [
                'columns'=> ["日期", "评论"],
                'rows' => []
            ],
            'week' => [
                'columns'=> ["日期", "评论"],
                'rows' => []
            ],
        ];
        for ($i=1; $i<=7; $i++) {
            $dayDate = date('Y-m-d', strtotime( '+' . $i-7 .' days'));
            $commentStats['day']['rows'][$dayDate] = [
                '日期' => $dayDate,
                '评论' => 0
            ];
            $weekDate = date('Y-W', strtotime( '+' . $i-7 .' week'));
            $commentStats['week']['rows'][$weekDate] = [
                '日期' => $weekDate,
                '评论' => 0
            ];
        }
        foreach ($result as $row) {
            $commentStats[$row->type]['rows'][$row->period]['评论'] = $row->total;
        }

        $commentStats['day']['rows'] = array_values($commentStats['day']['rows']);
        $commentStats['week']['rows'] = array_values($commentStats['week']['rows']);
        return response()->json($commentStats, 200);
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
