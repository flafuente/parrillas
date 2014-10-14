<?php

use Phinx\Migration\AbstractMigration;

class TriboSync extends AbstractMigration
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
        $table = $this->table('entradas');
        $table->addColumn('programaId', 'integer', array('after' => 'tipoId', 'limit' => 11, 'default' => 0, 'null' => true))
            ->addColumn('programa', 'string', array('after' => 'programaId', 'limit' => 200, 'default' => null, 'null' => true))
            ->addColumn('capitulo', 'string', array('after' => 'programa', 'limit' => 10, 'default' => null, 'null' => true))
            ->addColumn('titulo', 'string', array('after' => 'capitulo', 'limit' => 200, 'default' => null, 'null' => true))
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}
