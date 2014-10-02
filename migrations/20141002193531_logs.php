<?php

use Phinx\Migration\AbstractMigration;

class Logs extends AbstractMigration
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
        $table = $this->table('logs');
        $table->addColumn('userId',         'integer',  array('limit' => 11, 'default' => 0, 'null' => true))
            ->addColumn('actionId',         'integer',  array('limit' => 11, 'default' => 0, 'null' => true))
            ->addColumn('data',             'text',   array('default' => null, 'null' => true))
            ->addColumn('classAction',      'integer',  array('limit' => 11, 'default' => 0, 'null' => true))
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
