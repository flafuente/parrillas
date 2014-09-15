<?php

use Phinx\Migration\AbstractMigration;

class Base extends AbstractMigration
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
        // Usuarios
        if ($this->hasTable('users')) {
          $this->dropTable('users');
        }
        $table = $this->table('users');
        $table->addColumn('statusId',         'integer',  array('limit' => 11, 'default' => 0, 'null' => true))
            ->addColumn('username',         'string',   array('limit' => 32, 'default' => null, 'null' => true))
            ->addColumn('email',            'string',   array('limit' => 50, 'default' => null, 'null' => true))
            ->addColumn('password',         'string',   array('limit' => 32, 'default' => null, 'null' => true))
            ->addColumn('token',            'string',   array('limit' => 50, 'default' => null, 'null' => true))
            ->addColumn('lastvisitDate',    'datetime', array('default' => null, 'null' => true))
            ->addColumn('dateInsert',       'datetime', array('default' => null, 'null' => true))
            ->addColumn('dateUpdate',       'datetime', array('default' => null, 'null' => true))
            ->save();
        $this->execute("INSERT INTO users (statusId, username, email, password, dateInsert) VALUES ('1', 'admin', 'admin@admin.com', '0c7540eb7e65b553ec1ba6b20de79608', NOW())");

        // Entradas
        if ($this->hasTable('entradas')) {
          $this->dropTable('entradas');
        }
        $table = $this->table('entradas');
        $table->addColumn('nombre',         'string',   array('limit' => 100, 'default' => null, 'null' => true))
            ->addColumn('tipoId',           'integer',  array('limit' => 11, 'default' => 0, 'null' => true))
            ->addColumn('houseNumber',      'string',   array('limit' => 14, 'default' => null, 'null' => true))
            ->addColumn('segmento',         'integer',  array('limit' => 1, 'default' => 0, 'null' => true))
            ->addColumn('moscaId',          'integer',  array('limit' => 11, 'default' => 0, 'null' => true))
            ->addColumn('tcIn',             'string',   array('limit' => 11, 'default' => null, 'null' => true))
            ->addColumn('tcOut',            'string',   array('limit' => 11, 'default' => null, 'null' => true))
            ->addColumn('duracion',         'string',   array('limit' => 11, 'default' => null, 'null' => true))
            ->addColumn('entradaIdEd',      'integer',  array('limit' => 11, 'default' => 0, 'null' => true))
            ->addColumn('entradaIdFin',     'integer',  array('limit' => 11, 'default' => 0, 'null' => true))
            ->addColumn('dateInsert',       'datetime', array('default' => null, 'null' => true))
            ->addColumn('dateUpdate',       'datetime', array('default' => null, 'null' => true))
            ->save();

        // Tipos
        if ($this->hasTable('tipos')) {
          $this->dropTable('tipos');
        }
        $table = $this->table('tipos');
        $table->addColumn('nombre',         'string',   array('limit' => 100, 'default' => null, 'null' => true))
            ->addColumn('codigo',           'string',   array('limit' => 100, 'default' => null, 'null' => true))
            ->addColumn('color',            'string',   array('limit' => 100, 'default' => null, 'null' => true))
            ->addColumn('dateInsert',       'datetime', array('default' => null, 'null' => true))
            ->addColumn('dateUpdate',       'datetime', array('default' => null, 'null' => true))
            ->save();

        // Moscas
        if ($this->hasTable('moscas')) {
          $this->dropTable('moscas');
        }
        $table = $this->table('moscas');
        $table->addColumn('nombre',         'string',   array('limit' => 100, 'default' => null, 'null' => true))
            ->addColumn('codigo',           'string',   array('limit' => 100, 'default' => null, 'null' => true))
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
