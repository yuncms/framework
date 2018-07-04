<?php

use yii\db\Migration;

class m180223_102826Create_user_profile_table extends Migration
{
    public $tableName = '{{%user_profile}}';

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
         * 创建用户资料表
         */
        $this->createTable($this->tableName, [
            'user_id' => $this->integer()->unsigned()->notNull()->comment('User ID'),
            'gender' => $this->smallInteger(1)->notNull()->defaultValue(0)->comment('Gender'),
            'mobile' => $this->string()->comment('Mobile'),
            'email' => $this->string()->comment('Email'),
            'country' => $this->string()->comment('Country'),
            'province' => $this->string()->comment('Province'),
            'city' => $this->string()->comment('City'),
            'location' => $this->string()->comment('Location'),
            'address' => $this->string()->comment('Address'),
            'website' => $this->string()->comment('Website'),
            'timezone' => $this->string(100)->comment('Timezone'),//默认格林威治时间
            'birthday' => $this->string(15)->comment('Birthday'),
            'current' => $this->smallInteger(1)->comment('Current'),
            'qq' => $this->string(11)->comment('QQ'),
            'weibo' => $this->string(50)->comment('Weibo'),
            'wechat' => $this->string(50)->comment('Wechat'),
            'facebook' => $this->string(50)->comment('Facebook'),
            'twitter' => $this->string(50)->comment('Twitter'),
            'company' => $this->string()->comment('Company'),
            'company_job' => $this->string()->comment('Company Job'),
            'school' => $this->string()->comment('School'),
            'introduction' => $this->string()->comment('Introduction'),
            'bio' => $this->text()->comment('Bio'),
        ], $tableOptions);
        $this->addPrimaryKey('{{%user_profile_pk}}', $this->tableName, 'user_id');
        $this->addForeignKey('{{%user_profile_fk_1}}', $this->tableName, 'user_id', '{{%user}}', 'id', 'CASCADE', 'RESTRICT');

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
        echo "M180223102826Create_user_profile_table cannot be reverted.\n";

        return false;
    }
    */
}
