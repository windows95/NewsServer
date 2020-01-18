<?php

use Phalcon\Db\Column as Column;
use Phalcon\Db\Index as Index;
use Phalcon\Db\Reference as Reference;
use Phalcon\Migrations\Mvc\Model\Migration;

/**
* Создание таблицы с новостями
*
* @since 16.01.2020 21:42
* @author byrkin
*/
class NewsMigration_200 extends Migration
{
    public function up()
    {
        $this->morphTable(
            'news',
            [
                'columns' => [
                    new Column(
                        'id',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'size'          => 10,
                            'unsigned'      => true,
                            'notNull'       => true,
                            'autoIncrement' => true,
                        ]
                    ),
                    new Column(
                        'author_id',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'size'     => 10,
                            'unsigned' => true,
                            'notNull'  => true,
                        ]
                    ),
                    new Column(
                        'title',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 500,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'content',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 10000,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'created_at',
                        [
                            'type'    => Phalcon\Db\Column::TYPE_DATETIME,
                            'notNull' => true,
                        ]
                    ),
                ],
                'indexes' => [
                    new Index(
                        'PRIMARY',
                        [
                            'id',
                        ]
                    ),
                    new Index(
                        'author_id',
                        [
                            'author_id',
                        ]
                    ),
                ],
                'references' => [
                    new Reference(
                        'news_ibfk_1',
                        [
                            'referencedTable'   => 'authors',
                            'columns'           => ['author_id'],
                            'referencedColumns' => ['id'],
                        ]
                    ),
                ],
                'options' => [
                    'TABLE_TYPE'      => 'BASE TABLE',
                    'ENGINE'          => 'InnoDB',
                    'TABLE_COLLATION' => 'utf8_general_ci',
                ],
            ]
        );
    }
}