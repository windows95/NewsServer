<?php

use Api\Exceptions\WrongDataException as WrongDataException;

/**
* Контроллер для работы с авторами
*
* @since 17.01.2020 09:10
* @author byrkin
*/
class AuthorsController extends BaseController
{
    /**
     * Соответствие названиий параметров и полей модели
     * @var array
     */
    protected $map = ['last_name' => 'last_name', 'first_name' => 'first_name'];

    /**
     * Создание автора
     *
     * @since 17.01.2020 09:12
     * @author byrkin
     */
    public function createAction()
    {
        try
        {
            $author = new Authors();
            $this->saveOrCreateRecord($author, $this->map, $this->request->getJsonRawBody());

            $this->response->setStatusCode(201, 'Created');
            $this->response->setJsonContent(['id' => $author->id]);
        }
        catch (WrongDataException $e)
        {
            $this->response->setStatusCode(422, 'Unprocessable Entity');
            $this->response->setJsonContent(['error' => $e->getMessage()]);
        }
        catch (\Exception $e)
        {
            $this->response->setStatusCode(500, 'Internal Server Error');
            $this->response->setJsonContent(['error' => $e->getMessage()]);
        }
    }

    /**
     * updateAction
     *
     * @since 17.01.2020 09:12
     * @author byrkin
     */
    public function updateAction($id)
    {
        try
        {
            $author = Authors::findFirst(intval($id));
            $this->saveOrCreateRecord($author, $this->map, $this->request->getJsonRawBody());

            $this->response->setJsonContent(['id' => $id]);
        }
        catch (WrongDataException $e)
        {
            $this->response->setStatusCode(422, 'Unprocessable Entity');
            $this->response->setJsonContent(['error' => $e->getMessage()]);
        }
        catch (\Exception $e)
        {
            $this->response->setStatusCode(500, 'Internal Server Error');
            $this->response->setJsonContent(['error' => $e->getMessage()]);
        }
    }

    /**
     * listAction
     *
     * @since 17.01.2020 09:12
     * @author byrkin
     */
    public function listAction()
    {
        $data = [];
        foreach (Authors::find() as $author)
        {
            $data[] = [
                'id' => $author->id,
                'first_name' => $author->first_name,
                'last_name' => $author->last_name
            ];
        }
        $this->response->setJsonContent($data);
    }

    /**
     * deleteAction
     *
     * @since 17.01.2020 09:13
     * @author byrkin
     */
    public function deleteAction($id)
    {
        try
        {
            $author = Authors::findFirst(intval($id));

            if (!$author) {
                throw new \Exception('Author not found');
            }

            $author->delete();

            $this->response->setJsonContent(['id' => $id]);
        }
        catch (\Exception $e)
        {
            $this->response->setStatusCode(500, 'Internal Server Error');
            $this->response->setJsonContent(['error' => $e->getMessage()]);
        }
    }
}