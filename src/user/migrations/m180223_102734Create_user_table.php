<?php

use yuncms\db\Migration;

class m180223_102734Create_user_table extends Migration
{
    public $tableName = '{{%user}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB  AUTO_INCREMENT=10000000';
        }

        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned()->comment('ID'),
            'username' => $this->string(50)->notNull()->unique()->comment('Username'),
            'email' => $this->string(64)->unique()->comment('Email'),
            'mobile' => $this->string(11)->unique()->comment('Mobile'),
            'nickname' => $this->string()->notNull()->comment('Nickname'),
            'auth_key' => $this->string(100)->notNull()->comment('Auth Key'),
            'password_hash' => $this->string(100)->notNull()->comment('Password Hash'),
            'access_token' => $this->string(100)->notNull()->comment('Access Token'),
            'avatar' => $this->boolean()->defaultValue(false)->comment('Avatar'),
            'unconfirmed_email' => $this->string(150)->comment('Unconfirmed Email'),
            'unconfirmed_mobile' => $this->string(11)->comment('Unconfirmed Mobile'),
            'registration_ip' => $this->string()->comment('Registration Ip'),
            'identified' => $this->boolean()->defaultValue(false)->comment('Identified'),//是否经过实名认证
            'balance' => $this->decimal(12, 2)->defaultValue(0),//可提现余额
            'transfer_balance' => $this->decimal(12, 2)->defaultValue(0),//未结算余额
            'flags' => $this->integer()->defaultValue(0)->comment('Flags'),
            'email_confirmed_at' => $this->unixTimestamp()->comment('Email Confirmed At'),
            'mobile_confirmed_at' => $this->unixTimestamp()->comment('Mobile Confirmed At'),
            'blocked_at' => $this->unixTimestamp()->comment('Blocked At'),
            'created_at' => $this->unixTimestamp()->notNull()->comment('Created At'),
            'updated_at' => $this->unixTimestamp()->notNull()->comment('Updated At'),
        ], $tableOptions);
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
        echo "M180223102734Create_user_table cannot be reverted.\n";

        return false;
    }
    */
}
