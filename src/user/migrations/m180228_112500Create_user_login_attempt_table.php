<?php

use yuncms\db\Migration;

/**
 * Handles the creation of table `login_attempt`.
 */
class m180228_112500Create_user_login_attempt_table extends Migration
{
    public $tableName = '{{%user_login_attempt}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'key' => $this->string()->notNull(),
            'amount' => $this->integer(2)->defaultValue(1),
            'reset_at' => $this->unixTimestamp(),
            'created_at' => $this->unixTimestamp(),
            'updated_at' => $this->unixTimestamp(),
        ], $tableOptions);
        $this->createIndex('user_login_attempt_key_index', $this->tableName, 'key');
        $this->createIndex('user_login_attempt_amount_index', $this->tableName, 'amount');
        $this->createIndex('user_login_attempt_reset_at_index', $this->tableName, 'reset_at');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
