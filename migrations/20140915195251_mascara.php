<?php

use Phinx\Migration\AbstractMigration;

class Mascara extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */

    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('tipos');
        $table->addColumn('mascara', 'string', array('after' => 'codigo', 'limit' => 14, 'default' => null, 'null' => true))
              ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}
