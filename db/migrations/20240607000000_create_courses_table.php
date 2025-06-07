<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCoursesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('courses');
        $table->addColumn('title', 'string', ['limit' => 255])
              ->addColumn('description', 'text')
              ->addColumn('is_promoted', 'boolean', ['default' => false])
              ->addColumn('is_archived', 'boolean', ['default' => false])
              ->addColumn('created_at', 'datetime')
              ->addColumn('updated_at', 'datetime', ['null' => true])
              ->addIndex(['title'], ['unique' => true])
              ->create();
    }
}