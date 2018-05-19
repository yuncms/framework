<?php

use yuncms\db\Migration;

/**
 * Handles the creation of table `attachment`.
 */
class m180328_110823_create_attachment_table extends Migration
{
    /**
     * @var string The table name.
     */
    public $tableName = '{{%attachment}}';

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
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned()->comment('User ID'),
            'filename' => $this->string(255)->notNull()->comment('Filename'),
            'original_name' => $this->string(255)->notNull()->comment('Original Name'),
            'size' => $this->integer()->defaultValue(0)->comment('Size'),
            'type' => $this->string(255)->notNull()->comment('Type'),
            'path' => $this->string(255)->comment('Path'),
            'ip' => $this->string(255)->notNull()->comment('Ip'),
            'created_at' => $this->integer()->unsigned()->notNull()->comment('Created At'),
        ], $tableOptions);
        $this->addForeignKey('attachment_fk_1', $this->tableName, 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
