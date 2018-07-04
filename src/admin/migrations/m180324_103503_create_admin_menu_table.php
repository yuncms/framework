<?php

use yii\db\Query;
use yuncms\db\Migration;

/**
 * Handles the creation of table `admin_menu`.
 */
class m180324_103503_create_admin_menu_table extends Migration
{
    /**
     * @var string The table name.
     */
    public $tableName = '{{%admin_menu}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned()->comment('ID'),
            'name' => $this->string(128)->notNull()->comment('Name'),
            'parent' => $this->integer()->unsigned()->comment('Parent'),
            'route' => $this->string()->comment('Route'),
            'icon' => $this->string(30)->comment('Icon'),
            'visible' => $this->boolean()->defaultValue(true)->comment('Visible'),
            'sort' => $this->smallInteger()->defaultValue(99)->comment('Sort'),
            'data' => $this->text()->comment('Data')
        ], $tableOptions);

        $this->addForeignKey('admin_menu_fk_1', $this->tableName, 'parent', $this->tableName, 'id', 'SET NULL', 'CASCADE');

        $this->batchInsert($this->tableName, ['id', 'name', 'parent', 'route', 'icon', 'sort', 'data'], [
            //一级主菜单
            [1, '控制台', NULL, '/site/index', 'fa-th-large', 1, NULL],
            [2, '核心设置', NULL, NULL, 'fa-cog', 2, NULL],
            [3, '数据管理', NULL, NULL, 'fa-wrench', 3, NULL],
            [4, '运营中心', NULL, NULL, 'fa-bar-chart-o', 4, NULL],
            [5, '用户管理', NULL, NULL, 'fa-user', 5, NULL],
            [6, '网站管理', NULL, NULL, 'fa-bars', 6, NULL],
            [7, '财务管理', NULL, NULL, 'fa-cny', 7, NULL],
            [8, '模块管理', NULL, NULL, 'fa-th', 8, NULL],
            [9, '模板管理', NULL, NULL, 'fa-laptop', 9, NULL],

            //核心设置子菜单
            [21, '站点设置', 2, '/admin/setting/setting', 'fa-gears', 1, NULL],
            [22, '管理员管理', 2, '/admin/admin/index', 'fa-user', 2, NULL],
            [24, '角色管理', 2, '/admin/role/index', 'fa-group', 3, NULL],
            [25, '权限管理', 2, '/admin/permission/index', 'fa-certificate', 4, NULL],
            [26, '路由管理', 2, '/admin/route/index', 'fa-cloud', 5, NULL],
            [27, '规则管理', 2, '/admin/rule/index', 'fa-key', 6, NULL],
            [28, '菜单管理', 2, '/admin/menu/index', 'fa-wrench', 7, NULL],
            [29, '附件设置', 2, '/admin/attachment/setting', 'fa-cog', 8, NULL],
            [30, '执行计划', 2, '/admin/task/index', 'fa-spinner', 8, NULL],

            [50, '用户管理', 5, '/admin/user/index', 'fa-user', 2, NULL],
            [51, '角色管理', 5, '/admin/user-role/index', 'fa-group', 3, NULL],
            [52, '权限管理', 5, '/admin/user-permission/index', 'fa-certificate', 4, NULL],
            //[53, '路由管理', 5, '/admin/user-route/index', 'fa-cloud', 5, NULL],
            [54, '规则管理', 5, '/admin/user-rule/index', 'fa-key', 6, NULL],
            // [40, '地区管理', 3, '/area/index', 'fa-globe', 1, NULL],
            //[43, '敏感词管理', 3, '/admin/ban-word/index', 'fa-exclamation-triangle', 2, NULL],
        ]);

        //隐藏的子菜单[隐藏的子菜单不设置id字段，使用自增]//从10000开始
        $this->batchInsert($this->tableName, ['id', 'name', 'parent', 'route', 'visible', 'sort'], [
            [10000, '管理员查看', 22, '/admin/admin/view', 0, NULL],

        ]);
        $this->batchInsert($this->tableName, ['name', 'parent', 'route', 'visible', 'sort'], [
            ['更新管理员', 22, '/admin/admin/update', 0, NULL], ['授权设置', 22, '/admin/assignment/view', 0, NULL],
            ['角色查看', 24, '/admin/role/view', 0, NULL], ['创建角色', 24, '/admin/role/create', 0, NULL], ['更新角色', 24, '/admin/role/update', 0, NULL],
            ['权限查看', 25, '/admin/permission/view', 0, NULL], ['创建权限', 25, '/admin/permission/create', 0, NULL], ['更新权限', 25, '/admin/permission/update', 0, NULL],
            ['路由查看', 26, '/admin/route/view', 0, NULL], ['创建路由', 26, '/admin/route/create', 0, NULL],
            ['规则查看', 27, '/admin/rule/view', 0, NULL], ['创建规则', 27, '/admin/rule/create', 0, NULL], ['更新规则', 27, '/admin/rule/update', 0, NULL],
            ['菜单查看', 28, '/admin/menu/view', 0, NULL], ['创建菜单', 28, '/admin/menu/create', 0, NULL], ['更新菜单', 28, '/admin/menu/update', 0, NULL],
            //['创建地区', 40, '/area/create', 0, NULL], ['更新地区', 40, '/area/update', 0, NULL],
            //['敏感词查看', 43, '/admin/ban-word/view', 0, NULL], ['创建敏感词', 43, '/admin/ban-word/create', 0, NULL], ['更新敏感词', 43, '/admin/ban-word/update', 0, NULL],
        ]);

        $this->batchInsert($this->tableName, ['name', 'parent', 'route', 'visible', 'sort'], [
            ['用户设置', 50, '/admin/user/settings', 0, NULL],
            ['新建用户', 50, '/admin/user/create', 0, NULL],
            ['用户查看', 50, '/admin/user/view', 0, NULL],
            ['用户修改', 50, '/admin/user/update-profile', 0, NULL],
            ['账户详情', 50, '/admin/user/update', 0, NULL],
            ['权限分配', 50, '/admin/user-assignment/view', 0, NULL],

            //['授权设置', 52, '/admin/user-assignment/view', 0, NULL],
            ['角色查看', 51, '/admin/user-role/view', 0, NULL], ['创建角色', 51, '/admin/user-role/create', 0, NULL], ['更新角色', 51, '/admin/user-role/update', 0, NULL],
            ['权限查看', 52, '/admin/user-permission/view', 0, NULL], ['创建权限', 52, '/admin/user-permission/create', 0, NULL], ['更新权限', 52, '/admin/user-permission/update', 0, NULL],
            //['路由查看', 53, '/admin/user-route/view', 0, NULL], ['创建路由', 53, '/admin/user-route/create', 0, NULL],
            ['规则查看', 54, '/admin/user-rule/view', 0, NULL], ['创建规则', 54, '/admin/user-rule/create', 0, NULL], ['更新规则', 54, '/admin/user-rule/update', 0, NULL],
        ]);


        $this->insert($this->tableName, ['name' => '附件管理', 'parent' => 8, 'route' => '/admin/attachment/index', 'icon' => 'fa-cog', 'sort' => NULL, 'data' => NULL]);

        //OAuth2
        $this->insert($this->tableName, ['name' => 'App管理', 'parent' => 8, 'route' => '/admin/oauth2/index', 'icon' => 'fa fa-apple', 'sort' => NULL, 'data' => NULL]);
        $id = (new Query())->select(['id'])->from($this->tableName)->where(['name' => 'App管理', 'parent' => 8])->scalar($this->getDb());
        $this->batchInsert($this->tableName, ['name', 'parent', 'route', 'visible', 'sort'], [
            ['App查看', $id, '/admin/oauth2/view', 0, NULL],
        ]);


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
