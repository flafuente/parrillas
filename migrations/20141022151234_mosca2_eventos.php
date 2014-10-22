<?php

use Phinx\Migration\AbstractMigration;

class Mosca2Eventos extends AbstractMigration
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
        $table = $this->table('eventos');
        $table->addColumn('logo2', 'string', array('after' => 'logo', 'limit' => 50, 'default' => null, 'null' => true))
            ->addColumn('delay', 'string', array('after' => 'logo2', 'limit' => 11, 'default' => null, 'null' => true))
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}
