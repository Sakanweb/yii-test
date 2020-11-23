<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m201119_150923_import_history
 */
class m201119_150923_import_history extends Migration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('import_history', [
            'id' => Schema::TYPE_PK,
            'store_id' => Schema::TYPE_INTEGER . ' not null',
            'total' => Schema::TYPE_INTEGER,
            'succeed' => Schema::TYPE_INTEGER,
            'status' => "ENUM('success', 'wrong')",
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
        ], $tableOptions);

        // add foreign keys for data integrity
        $this->addForeignKey('import_history_id', 'import_history', 'store_id', 'store', 'id');
    }

    public function down()
    {
        $this->dropTable('import_history');
    }
}
