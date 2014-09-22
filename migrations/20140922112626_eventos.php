<?php

use Phinx\Migration\AbstractMigration;

class Eventos extends AbstractMigration
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
        // Eventos
        $table = $this->table('eventos');
        $table->addColumn('order',          'integer',  array('limit' => 11, 'default' => 0, 'null' => true))
            ->addColumn('userId',           'integer',  array('limit' => 11, 'default' => 0, 'null' => true))
            ->addColumn('entradaId',        'integer',  array('limit' => 11, 'default' => 0, 'null' => true))
            ->addColumn('houseNumber',      'string',   array('limit' => 14, 'default' => null, 'null' => true))
            ->addColumn('tipo',             'string',   array('limit' => 50, 'default' => null, 'null' => true))
            ->addColumn('titulo',           'string',   array('limit' => 50, 'default' => null, 'null' => true))
            ->addColumn('tcIn',             'string',   array('limit' => 50, 'default' => null, 'null' => true))
            ->addColumn('logo',             'string',   array('limit' => 50, 'default' => null, 'null' => true))
            ->addColumn('segmento',         'integer',  array('limit' => 4, 'default' => 0, 'null' => true))
            ->addColumn('fecha',            'date',     array('default' => null, 'null' => true))
            ->addColumn('inicio',           'time',     array('default' => null, 'null' => true))
            ->addColumn('fin',              'time',     array('default' => null, 'null' => true))
            ->addColumn('duracion',         'string',   array('limit' => 50, 'default' => null, 'null' => true))
            ->addColumn('dateInsert',       'datetime', array('default' => null, 'null' => true))
            ->addColumn('dateUpdate',       'datetime', array('default' => null, 'null' => true))
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}
