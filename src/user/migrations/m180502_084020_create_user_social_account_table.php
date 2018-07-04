<?php

use yuncms\db\Migration;

/**
 * Handles the creation of table `user_social_account`.
 */
class m180502_084020_create_user_social_account_table extends Migration
{
    /**
     * @var string The table name.
     */
    public $tableName = '{{%user_social_account}}';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        /**
         * 创建社交账户表
         */
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned()->comment('ID'),
            'user_id' => $this->integer()->unsigned()->comment('Uer ID'),
            'username' => $this->string()->comment('Username'),
            'email' => $this->string()->comment('EMail'),
            'provider' => $this->string(50)->notNull()->comment('Provider'),
            'client_id' => $this->string(100)->notNull()->comment('Client Id'),
            'code' => $this->string(32)->unique()->comment('Code'),
            'created_at' => $this->unixTimestamp()->notNull()->comment('Created At'),
            'data' => $this->text()->comment('Data'),
        ], $tableOptions);
        $this->createIndex('account_unique', $this->tableName, ['provider', 'client_id'], true);
        $this->addForeignKey('{{%user_account_fk_1}}', $this->tableName, 'user_id', '{{%user}}', 'id', 'CASCADE', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
