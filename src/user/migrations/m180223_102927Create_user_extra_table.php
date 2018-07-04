<?php

use yuncms\db\Migration;

class m180223_102927Create_user_extra_table extends Migration
{
    public $tableName = '{{%user_extra}}';

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
        /**
         * 创建用户附表
         */
        $this->createTable($this->tableName, [
            'user_id' => $this->userId()->notNull()->comment('User ID'),
            'login_ip' => $this->ipAddress()->comment('Login Ip'),
            'login_at' => $this->unixTimestamp()->comment('Login At'),
            'login_num' => $this->counter()->comment('Login Num'),
            'last_visit' => $this->counter()->comment('Last Visit'),
            'views' => $this->counter()->comment('Views'),
            'supports' => $this->counter()->comment('Supports'),
            'followers' => $this->counter()->comment('Followers'),
            'collections' => $this->counter()->comment('Collections'),
        ], $tableOptions);
        $this->addPrimaryKey('{{%user_extra_pk}}', $this->tableName, 'user_id');
        $this->addForeignKey('{{%user_extra_fk_1}}', $this->tableName, 'user_id', '{{%user}}', 'id', 'CASCADE', 'RESTRICT');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M180223102927Create_user_extra_table cannot be reverted.\n";

        return false;
    }
    */
}
