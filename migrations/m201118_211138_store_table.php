<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m201118_211138_store_table
 */
class m201118_211138_store_table extends Migration
{
    /**
     * @return bool|void|null
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('store', [
            'id' => Schema::TYPE_PK,
            'title' => Schema::TYPE_STRING . ' not null'
        ], $tableOptions);
    }

    /**
     * @return bool|void|null
     */
    public function down()
    {
        $this->dropTable('store');
    }

}
