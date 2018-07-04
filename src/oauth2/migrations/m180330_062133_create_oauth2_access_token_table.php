<?php

use yuncms\db\Migration;

/**
 * Handles the creation of table `oauth2_access_token`.
 */
class m180330_062133_create_oauth2_access_token_table extends Migration
{
    /**
     * @var string The table name.
     */
    public $tableName = '{{%oauth2_access_token}}';

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
            'access_token' => $this->string(40)->notNull()->comment('Access Token'),
            'client_id' => $this->integer()->unsigned()->notNull()->comment('Client Id'),
            'user_id' => $this->integer()->unsigned()->comment('User Id'),
            'expires' => $this->integer()->notNull()->comment('Expires'),
            'scope' => $this->text()->comment('Scope'),
        ], $tableOptions);
        $this->addPrimaryKey('pk', $this->tableName, 'access_token');
        $this->createIndex('ix_access_token_expires', $this->tableName, 'expires');
        $this->addforeignkey('fk_access_token_oauth2_client_id', $this->tableName, 'client_id', '{{%oauth2_client}}', 'client_id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
