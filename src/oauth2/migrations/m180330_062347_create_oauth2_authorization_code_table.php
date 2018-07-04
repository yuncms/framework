<?php

use yuncms\db\Migration;

/**
 * Handles the creation of table `oauth2_authorization_code`.
 */
class m180330_062347_create_oauth2_authorization_code_table extends Migration
{
    /**
     * @var string The table name.
     */
    public $tableName = '{{%oauth2_authorization_code}}';

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
            'authorization_code' => $this->string(40)->notNull()->comment('Authorization Code'),
            'client_id' => $this->integer()->unsigned()->notNull()->comment('Client Id'),
            'user_id' => $this->integer()->unsigned()->comment('User Id'),
            'redirect_uri' => $this->text()->notNull()->comment('Redirect Uri'),
            'expires' => $this->integer()->notNull()->comment('Expires'),
            'scope' => $this->text()->comment('Scope'),
        ],$tableOptions);

        $this->addPrimaryKey('pk', $this->tableName, 'authorization_code');
        $this->createIndex('ix_authorization_code_expires', $this->tableName, 'expires');
        $this->addforeignkey('fk_authorization_code_oauth2_client_id', $this->tableName, 'client_id', '{{%oauth2_client}}', 'client_id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
