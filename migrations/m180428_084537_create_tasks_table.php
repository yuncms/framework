<?php

use yuncms\db\Migration;

/**
 * Handles the creation of table `tasks`.
 */
class m180428_084537_create_tasks_table extends Migration
{
    /**
     * @var string The table name.
     */
    public $tableName = '{{%tasks}}';

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
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull(),//定时任务名称
            'route' => $this->string(100)->notNull(),//任务路由
            'crontab_str' => $this->string(50)->notNull(),//crontab格式
            'switch' => $this->boolean()->notNull()->defaultValue(false),//任务开关 0关闭 1开启
            'status' => $this->boolean()->defaultValue(false),//任务运行状态 0正常 1任务报错
            'last_rundate' => $this->dateTime()->null(),//任务上次运行时间
            'next_rundate' => $this->dateTime()->null(),//任务下次运行时间
            'execmemory' => $this->decimal(9,2)->defaultValue('0.00'),//任务执行消耗内存(单位/byte)
            'exectime' => $this->decimal(9,2)->defaultValue('0.00'),//任务执行消耗时间
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
