<?php

use Phalcon\Mvc\Controller;

/**
* Контроллер с базовой логикой
*
* @since 16.01.2020 21:01
* @author byrkin
*/
class BaseController extends Controller
{
    /**
     * Инициализация контроллера
     *
     * @since 16.01.2020 21:03
     * @author byrkin
     */
    public function initialize()
    {
        $this->view->disable();
        // Default HTTP status
        $this->response->setStatusCode(200, 'OK');
    }

    /**
     * Выполняется после вызова каждого экшена
     *
     * @since 17.01.2020 20:41
     * @author byrkin
     * @param Phalcon\Mvc\Dispatcher $disable
     */
    public function afterExecuteRoute(Phalcon\Mvc\Dispatcher $dispatcher)
    {
        $this->response->send();
    }
}
