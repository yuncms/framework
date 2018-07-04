<?php

use yuncms\db\Migration;
use yuncms\admin\models\Admin;

/**
 * Handles the creation of table `admin`.
 */
class m180324_102044_create_admin_table extends Migration
{
    /**
     * @var string table name
     */
    public $tableName = '{{%admin}}';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->comment('ID'),
            'username' => $this->string(50)->notNull()->unique()->comment('Username'),
            'email' => $this->string(60)->notNull()->unique()->comment('EMail'),
            'mobile' => $this->string(11)->notNull()->unique()->comment('Mobile'),
            'auth_key' => $this->string(32)->notNull()->comment('Auth Key'),
            'password_hash' => $this->string()->notNull()->comment('Password Hash'),
            'access_token' => $this->string()->unique()->comment('Access Token'),
            'status' => $this->boolean()->defaultValue(true)->comment('Status'),
            'last_login_at' => $this->unixTimestamp()->comment('Last Login At'),
            'created_at' => $this->unixTimestamp()->notNull()->comment('Created At'),
            'updated_at' => $this->unixTimestamp()->notNull()->comment('Updated At'),
        ], $tableOptions);

        //添加默认超级管理员帐户 密码是 123456
        Admin::create([
            'username' => 'admin',
            'email' => 'xutongle@gmail.com',
            'mobile' => '13800138000',
            'password' => '123456',
            'status' => 0b0,
        ]);
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
