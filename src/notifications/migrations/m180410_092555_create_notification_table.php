<?php

use yuncms\db\Migration;

/**
 * Handles the creation of table `notification`.
 */
class m180410_092555_create_notification_table extends Migration
{
    /**
     * @var string The table name.
     */
    public $tableName = '{{%notifications}}';

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
        // https://segmentfault.com/q/1010000000672529/a-1020000000679702
        $this->createTable($this->tableName, [
            'id' => $this->char(24)->notNull()->comment('Id'),//通知ID
            'verb' => $this->string(32),//执行了什么操作
            'template' => $this->string(),//模板
            'notifiable_id' => $this->unsignedInteger()->notNull()->comment('Entity'),//通知实体ID
            'notifiable_class' => $this->string()->notNull()->comment('Entity'),//通知实体类名
            'data' => $this->text(),//通知数据

            // data_entity 触发通知的那个实体
            // data_source 源实体
            // data_target 目标实体
            //
            'read_at' => $this->unixTimestamp()->comment('Read At'),//阅读时间
            'created_at' => $this->unixTimestamp()->notNull()->comment('Created At'),//创建时间
            'updated_at' => $this->integer(10)->unsigned()->notNull()->comment('Updated At'),//更新时间
        ], $tableOptions);
        $this->addPrimaryKey('{{%notification_notifiable_pk}}', $this->tableName, 'id');
        $this->createIndex('notification_notifiable', $this->tableName, ['notifiable_id', 'notifiable_class']);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
