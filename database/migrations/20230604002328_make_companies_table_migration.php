<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class MakeCompaniesTableMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {

        $table = $this->table('companies');
        $table
            ->addColumn('internal_id', 'integer', ['limit' => 128])
            ->addColumn('other_id', 'string', ['limit' => 128])
            ->addColumn('company_social_name', 'string')
            ->addColumn('company_comercial_name', 'string')
            ->addColumn('email', 'string', ['limit' => 128])
            ->addColumn('nif', 'string', ['limit' => 128])
            ->addColumn('permit', 'string', ['limit' => 128])
            ->addColumn('linkedin', 'string')
            ->addColumn('facebook', 'string')
            ->addColumn('instagram', 'string')
            ->addColumn('youtube', 'string')
            ->addColumn('twitter', 'string')
            ->addColumn('google', 'string')
            ->addColumn('value_paid', 'float', ['limit' => 128])
            ->addColumn('observations', 'text')
            ->addColumn('contract_start', 'date', ['limit' => 128])
            ->addColumn('contract_end', 'date', ['limit' => 128])
            ->addTimestamps()
            ->create();
    }
}
