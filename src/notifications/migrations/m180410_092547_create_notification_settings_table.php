<?php

use yuncms\db\Migration;

/**
 * Handles the creation of table `notification_settings`.
 */
class m180410_092547_create_notification_settings_table extends Migration
{
    /**
     * @var string The table name.
     */
    public $tableName = '{{%notification_settings}}';

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
            'user_id' => $this->integer()->unsigned()->notNull()->comment('User Id'),
            'category' => $this->string(200)->notNull()->comment('Category'),
            'screen' => $this->boolean()->defaultValue(true)->comment('Screen'),
            'email' => $this->boolean()->defaultValue(true)->comment('Email'),
            'sms' => $this->boolean()->defaultValue(true)->comment('Sms'),
            'app' => $this->boolean()->defaultValue(true)->comment('App'),
            'updated_at' => $this->integer(10)->unsigned()->notNull()->comment('Updated At'),
        ], $tableOptions);
        $this->addPrimaryKey('notification_settings_pk', $this->tableName, 'user_id');
        $this->createIndex('notification_settings_index', $this->tableName, ['user_id', 'category'], true);
        $this->addForeignKey('notification_settings_fk_1', $this->tableName, 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
