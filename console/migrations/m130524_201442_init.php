<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),

            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
        ], $tableOptions);

        $this->createTable('{{%email}}', [
            'id' => $this->primaryKey(),
            'email' => $this->string()->notNull(),
            'password' => $this->string()->notNull(),
            'type' => $this->string()->notNull(),
            'import' => $this->smallInteger()->notNull()->defaultValue(0),

            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
        $this->dropTable('{{%email}}');
    }
}
