<?php

use yuncms\db\Migration;

/**
 * Handles the creation of table `oauth2_refresh_token`.
 */
class m180330_062249_create_oauth2_refresh_token_table extends Migration
{
    /**
     * @var string The table name.
     */
    public $tableName = '{{%oauth2_refresh_token}}';

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
            'refresh_token' => $this->string(40)->notNull()->comment('Refresh Token'),
            'client_id' => $this->integer()->unsigned()->notNull()->comment('Client Id'),
            'user_id' => $this->integer()->unsigned()->comment('User Id'),
            'expires' => $this->integer()->notNull()->comment('Expires'),
            'scope' => $this->text()->comment('Scope'),
        ],$tableOptions);

        $this->addPrimaryKey('pk', $this->tableName, 'refresh_token');
        $this->createIndex('ix_refresh_token_expires', $this->tableName, 'expires');
        $this->addforeignkey('fk_refresh_token_oauth2_client_id', $this->tableName, 'client_id', '{{%oauth2_client}}', 'client_id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
