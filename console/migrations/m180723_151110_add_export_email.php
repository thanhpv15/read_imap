<?php

use yii\db\Migration;

class m180723_151110_add_export_email extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->addColumn('{{%email}}', 'export', $this->smallInteger()->notNull()->defaultValue(0));
    }

    public function down()
    {
        $this->dropColumn('{{%email}}', 'export');
    }
}
