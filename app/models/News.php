<?php

use Phalcon\Mvc\Model;

/**
* Модель для хранения новостей
*
* @since 16.01.2020 20:37
* @author byrkin
*/
class News extends Model
{
    public $id;
    public $author_id;
    public $title;
    public $content;

    /**
     * Comments
     *
     * @since 17.01.2020 22:15
     * @author byrkin
     */
    public function initialize()
    {
        $this->belongsTo(
            'author_id',
            'Authors',
            'id',
            [
                'reusable' => true,
                'alias' => 'author'
            ]
        );
    }
}