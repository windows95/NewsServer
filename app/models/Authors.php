<?php

use Phalcon\Mvc\Model;

/**
* Модель для хранения авторов новостей
*
* @since 16.01.2020 20:30
* @author byrkin
*/
class Authors extends Model
{
    // Правильнее через сеттеры и геттеры.
    // Сделано через публичные свойства чтобы не разводить пустую писанину.
    public $id;
    public $first_name;
    public $last_name;

    /**
     * Comments
     *
     * @since 17.01.2020 22:13
     * @author byrkin
     */
    public function initialize()
    {
        $this->hasMany(
            'id',
            'News',
            'author_id',
            [
                'reusable' => true,
                'alias' => 'news'
            ]
        );
    }
}