<?php

use Phalcon\Mvc\Controller;
use Api\Exceptions\WrongDataException as WrongDataException;

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
     * saveOrCreateRecord
     *
     * @since 17.01.2020 11:14
     * @author byrkin
     * @param Phalcon\Mvc\Model $recode
     * @param array $map
     * @param stdClass $data
     */
    protected function saveOrCreateRecord(Phalcon\Mvc\Model $record, array $map, stdClass $data)
    {
        $missingFields = [];
        foreach ($map as $paramName => $fieldName)
        {
            if (!property_exists($data, $paramName)) {
                $missingFields[] = $paramName;
            }
            else {
                $record->{$fieldName} = $data->{$paramName};
            }
        }

        if (count($missingFields)) {
            throw new WrongDataException('Missing params: ' . implode(', ', $missingFields));
        }

        if ($record->save() === false)
        {
            $errors = array_map(function($message) {
                return $message->getMessage();
            }, $newsItem->getMessages());

            throw new WrongDataException(implode('; ', $errors));
        }
        $data->id = $record->id;
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
