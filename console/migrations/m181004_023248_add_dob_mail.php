<?php

use yii\db\Migration;

/**
 * Class m181004_023248_add_dob_mail
 */
class m181004_023248_add_dob_mail extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->addColumn('{{%email}}', 'dob', $this->string()->defaultValue(""));
    }

    public function down()
    {
        $this->dropColumn('{{%email}}', 'dob');
    }
}
