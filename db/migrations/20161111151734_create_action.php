<?php

use Phinx\Db\Adapter\PdoAdapter;
use Phinx\Migration\AbstractMigration;

class CreateAction extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('action');
        $table
            ->addColumn('game_id', PdoAdapter::PHINX_TYPE_INTEGER)
            ->addForeignKey('game_id', 'game', 'id', ['delete'=> 'CASCADE', 'update'=> 'CASCADE'])
            ->addColumn('action', PdoAdapter::PHINX_TYPE_INTEGER)
            ->addColumn('created', PdoAdapter::PHINX_TYPE_INTEGER)
            ->create();
    }
    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('action');
    }
}
