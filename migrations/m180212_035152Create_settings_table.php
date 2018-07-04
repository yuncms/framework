<?php

use yuncms\db\Migration;

class m180212_035152Create_settings_table extends Migration
{
    public $tableName = '{{%settings}}';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'type' => $this->string(255)->notNull(),
            'section' => $this->string(255)->notNull(),
            'key' => $this->string(255)->notNull(),
            'value' => $this->text(),
            'active' => $this->boolean(),
            'created' => $this->dateTime(),
            'modified' => $this->dateTime(),
        ], $tableOptions);

        $this->createIndex('{{%settings_unique_key_section}}', $this->tableName, ['section', 'key'], true);
    }

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
        echo "M180212035152Create_settings_table cannot be reverted.\n";

        return false;
    }
    */
}
