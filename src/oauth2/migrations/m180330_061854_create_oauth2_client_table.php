<?php

use yuncms\db\Migration;

/**
 * Handles the creation of table `oauth2_client`.
 */
class m180330_061854_create_oauth2_client_table extends Migration
{
    /**
     * @var string The table name.
     */
    public $tableName = '{{%oauth2_client}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB AUTO_INCREMENT=100000';
        }

        $this->createTable($this->tableName, [
            'client_id' => $this->primaryKey()->unsigned()->comment('Client ID'),
            'client_secret' => $this->string(64)->comment('Client Secret'),
            'user_id' => $this->integer()->unsigned()->comment('User ID'),
            'redirect_uri' => $this->text()->notNull()->comment('Redirect URL'),
            'grant_type' => $this->text()->comment('Grant Type'),
            'scope' => $this->text()->comment('Scope'),
            'name' => $this->string()->comment('Name'),
            'domain' => $this->string()->comment('Domain'),
            'provider' => $this->string()->comment('Provider'),
            'icp' => $this->string()->comment('ICP'),
            'registration_ip' => $this->string()->comment('Registration Ip'),
            'created_at' => $this->integer()->comment('Created At'),
            'updated_at' => $this->integer()->comment('Updated At'),
        ], $tableOptions);

        $this->createIndex('oauth2_client_unique', $this->tableName, ['client_id', 'client_secret'], true);
        $this->addforeignkey('oauth2_client_user_id_fk', $this->tableName, 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
