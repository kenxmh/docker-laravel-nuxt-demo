<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSystemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 后台用户和角色
        DB::table('admin_user')->truncate();
        DB::table('admin_role')->truncate();
        DB::table('admin_user_role')->truncate();
        DB::table('admin_access')->truncate();
        DB::table('admin_role_access')->truncate();

        $now = date('Y-m-d H:i:s');

        DB::table('admin_user')->insert([
            ['id' => 1, 'username' => 'admin', 'password' => bcrypt('123456'), 'realname' => 'admin'],
        ]);

        DB::table('admin_role')->insert([
            ['id' => 1, 'name' => 'admin'],
        ]);
        DB::table('admin_user_role')->insert([
            ['id' => 1, 'admin_id' => 1, 'role_id' => 1],
        ]);

        $baseAction = [
            ['action' => '', 'action_name' => ''],
            ['action' => 'index', 'action_name' => '列表'],
            ['action' => 'store', 'action_name' => '创建'],
            ['action' => 'show', 'action_name' => '详情'],
            ['action' => 'update', 'action_name' => '更新'],
            ['action' => 'destroy', 'action_name' => '删除'],
        ];

        $accessArr = [
            [
                'name'       => '个人',
                'group'      => 'Auth',
                'controller' => 'Admin\\AuthController',
                'action'     => [
                    ['action' => '', 'action_name' => ''],
                    ['action' => 'updateUser', 'action_name' => '修改信息'],
                ],
            ],
            [
                'name'       => '后台用户',
                'group'      => 'Admin',
                'controller' => 'Admin\\UserController',
                'action'     => array_merge($baseAction, [
                    ['action' => 'resetPassword', 'action_name' => '重置密码'],
                    ['action' => 'resetRole', 'action_name' => '修改角色'],
                ]),
            ],
            [
                'name'       => '角色',
                'group'      => 'Role',
                'controller' => 'Admin\\RoleController',
                'action'     => array_merge($baseAction, [
                    ['action' => 'getAccesses', 'action_name' => '获取角色的路由规则'],
                ]),
            ],
            [
                'name'       => '路由规则',
                'group'      => 'Access',
                'controller' => 'Admin\\AccessController',
                'action'     => $baseAction,
            ],
            [
                'name'       => '统计',
                'group'      => 'Stat',
                'controller' => 'Admin\\StatController',
                'action'     => [
                    ['action' => '', 'action_name' => ''],
                    ['action' => 'index', 'action_name' => '基本数据'],
                ],
            ],
            [
                'name'       => '图片',
                'group'      => 'Image',
                'controller' => 'Admin\\ImageController',
                'action'     => [
                    ['action' => '', 'action_name' => ''],
                    ['action' => 'store', 'action_name' => '上传图片'],
                ],
            ],
            [
                'name'       => '文章',
                'group'      => 'Article',
                'controller' => 'Admin\\ArticleController',
                'action'     => $baseAction,
            ],
            [
                'name'       => '文章分类',
                'group'      => 'Article',
                'controller' => 'Admin\\CategoryController',
                'action'     => $baseAction,
            ],
            [
                'name'       => '文章评论',
                'group'      => 'Article',
                'controller' => 'Admin\\ArticleCommentController',
                'action'     => [
                    ['action' => '', 'action_name' => ''],
                    ['action' => 'index', 'action_name' => '列表'],
                    ['action' => 'store', 'action_name' => '回复'],
                ],
            ],
        ];

        $finalAccessArr = $finalRoleAccessArr = [];
        $index          = 1;
        foreach ($accessArr as $row) {
            foreach ($row['action'] as $item) {
                if (empty($item['action'])) {
                    $pid = $index;
                }
                $finalAccessArr[] = [
                    'id'         => $index,
                    'pid'        => !empty($item['action']) ? $pid : 0,
                    'name'       => !empty($item['action']) ? $row['name'] . '-' . $item['action_name'] : $row['name'],
                    'action'     => !empty($item['action']) ? $row['controller'] . '@' . $item['action'] : '',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $finalRoleAccessArr[] = [
                    'id'        => $index,
                    'role_id'   => 1,
                    'access_id' => $index,
                ];
                $index += 1;
            }
        }
        DB::table('admin_access')->insert($finalAccessArr);
        DB::table('admin_role_access')->insert($finalRoleAccessArr);
    }
}
