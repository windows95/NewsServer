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
     * saveOrCreateAuthor
     *
     * @since 17.01.2020 10:52
     * @author byrkin
     * @param object $data
     * @param int $id
     * @return Authors $author
     */
    protected function saveOrCreateAuthor($data, $id = null)
    {
        $author = $id ? Authors::findFirst($id) : new Authors();

        if ($id && !$author) {
            throw new WrongDataException('Author not found');
        }

        $missingFields = [];
        foreach (['first_name', 'last_name'] as $field)
        {
            if (!property_exists($data, $field)) {
                $missingFields[] = $field;
            }
        }

        if (count($missingFields)) {
            throw new WrongDataException('Fields missing: ' . implode(', ', $missingFields));
        }

        $author->first_name = $data->first_name;
        $author->last_name = $data->last_name;

        if ($author->save() === false)
        {
            $errors = array_map(function($message) {
                return $message->getMessage();
            }, $author->getMessages());

            throw new WrongDataException(implode('; ', $errors));
        }
        return $author;
    }

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
            $data = $this->request->getJsonRawBody();

            $author = $this->saveOrCreateAuthor($data);

            $data->id = $author->id;

            $this->response->setStatusCode(201, 'Created');
            $this->response->setJsonContent($data);
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
     * updateAction
     *
     * @since 17.01.2020 09:12
     * @author byrkin
     */
    public function updateAction($id)
    {
        try
        {
            $data = $this->request->getJsonRawBody();

            $this->saveOrCreateAuthor($data, intval($id));

            $this->response->setJsonContent($data);
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