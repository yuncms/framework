<?php

use yuncms\db\Migration;

/**
 * Handles the creation of table `rbac`.
 */
class m180411_035330_create_rbac_table extends Migration
{
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
        $this->createTable('{{%user_auth_rule}}', [
            'name' => $this->string(64)->notNull()->unique(),
            'data' => $this->text(),
            'created_at' => $this->unixTimestamp(),
            'updated_at' => $this->unixTimestamp()
        ], $tableOptions);

        $this->createTable('{{%user_auth_item}}', [
            'name' => $this->string(64)->notNull()->unique(),
            'type' => $this->integer()->notNull(),
            'description' => $this->text(),
            'rule_name' => $this->string(64),
            'data' => $this->text(),
            'created_at' => $this->unixTimestamp(),
            'updated_at' => $this->unixTimestamp(),
        ], $tableOptions);
        $this->createIndex('idx-auth_item-type', '{{%user_auth_item}}', 'type');
        $this->addForeignKey('user_auth_item_fk_1', '{{%user_auth_item}}', 'rule_name', '{{%user_auth_rule}}', 'name', 'SET NULL', 'CASCADE');

        $this->createTable('{{%user_auth_item_child}}', [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
        ], $tableOptions);
        $this->addPrimaryKey('user_auth_item_child_pk', '{{%user_auth_item_child}}', ['parent', 'child']);
        $this->addForeignKey('user_auth_item_child_fk_1', '{{%user_auth_item_child}}', 'parent', '{{%user_auth_item}}', 'name', 'CASCADE', 'CASCADE');
        $this->addForeignKey('user_auth_item_child_fk_2', '{{%user_auth_item_child}}', 'child', '{{%user_auth_item}}', 'name', 'CASCADE', 'CASCADE');

        $this->createTable('{{%user_auth_assignment}}', [
            'item_name' => $this->string(64)->notNull(),
            'user_id' => $this->string(64)->notNull(),
            'created_at' => $this->unixTimestamp(),
        ], $tableOptions);
        $this->addPrimaryKey('', '{{%user_auth_assignment}}', ['item_name', 'user_id']);
        $this->addForeignKey('user_auth_assignment_fk_1', '{{%user_auth_assignment}}', 'item_name', '{{%user_auth_item}}', 'name', 'CASCADE', 'CASCADE');

        $time = time();
        $this->batchInsert('{{%user_auth_item}}', ['name', 'type', 'created_at', 'updated_at'], [
            ['受限会员', 1, $time, $time],
            ['注册会员', 1, $time, $time],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_auth_assignment}}');
        $this->dropTable('{{%user_auth_item_child}}');
        $this->dropTable('{{%user_auth_item}}');
        $this->dropTable('{{%user_auth_rule}}');
    }
}
