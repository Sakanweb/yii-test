<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m201118_211241_store_product_table
 */
class m201118_211241_store_product_table extends Migration
{

    public function up()
    {
      $tableOptions = null;
      if ($this->db->driverName === 'mysql') {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
      }
      $this->createTable('store_product', [
        'id' => Schema::TYPE_PK,
        'store_id' => Schema::TYPE_INTEGER . ' not null',
        'upc' => Schema::TYPE_STRING . ' not null UNIQUE',
        'title' => Schema::TYPE_STRING,
        'price' => Schema::TYPE_STRING,
      ], $tableOptions);

      // add foreign keys for data integrity
      $this->addForeignKey('store_product_id', 'store_product', 'store_id', 'store', 'id');
    }

    public function down()
    {
      $this->dropTable('store_product');
    }

}
