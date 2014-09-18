<?php

use Phinx\Migration\AbstractMigration;

class Moscas2 extends AbstractMigration
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
        $table = $this->table('moscas');
        $table->addColumn('tipoId', 'integer', array('after' => 'id', 'limit' => 11, 'default' => 0, 'null' => true))
            ->save();

        $table = $this->table('entradas');
        $table->addColumn('moscaId2', 'integer', array('after' => 'moscaId', 'limit' => 11, 'default' => 0, 'null' => true))
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}
