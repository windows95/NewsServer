<?php

use Api\Exceptions\WrongDataException as WrongDataException;

/**
* Контроллер для работы с новостями
*
* @since 16.01.2020 21:11
* @author byrkin
*/
class NewsController extends BaseController
{
    /**
     * saveOrCreateNewsItem
     *
     * @since 17.01.2020 11:14
     * @author byrkin
     * @param object $data
     * @param int $id
     * @return News
     */
    protected function saveOrCreateNewsItem($data, $id = null)
    {
        $newsItem = $id ? News::findFirst($id) : new News();

        if ($id && !$newsItem) {
            throw new WrongDataException('News item not found');
        }

        $missingFields = [];
        foreach (['author_id', 'title', 'content'] as $field)
        {
            if (!property_exists($data, $field)) {
                $missingFields[] = $field;
            }
        }

        if (count($missingFields)) {
            throw new WrongDataException('Fields missing: ' . implode(', ', $missingFields));
        }

        $newsItem->author_id = $data->author_id;
        $newsItem->title = $data->title;
        $newsItem->content = $data->content;

        if (!$id) {
            $newsItem->created_at = date('Y-m-d H:i:s');
        }

        if ($newsItem->save() === false)
        {
            $errors = array_map(function($message) {
                return $message->getMessage();
            }, $newsItem->getMessages());

            throw new WrongDataException(implode('; ', $errors));
        }
        return $newsItem;
    }

    /**
     * Список новостей
     *
     * @since 16.01.2020 21:11
     * @author byrkin
     */
    public function listAction()
    {
        $news = News::find(['order' => 'created_at DESC']);

        $this->response->setJsonContent($this->prepareList($news));
    }

    /**
     * listByAuthorAction
     *
     * @since 17.01.2020 11:11
     * @author byrkin
     */
    public function listByAuthorAction($authorId)
    {
        $news = News::find([
            'conditions' => 'author_id = ?0',
            'bind' => [intval($authorId)],
            'order' => 'created_at DESC'
        ]);

        $this->response->setJsonContent($this->prepareList($news));
    }

    /**
     * prepareList
     *
     * @since 17.01.2020 20:06
     * @author byrkin
     * @param Phalcon\Mvc\Model\Resultset\Simple $news
     * @return array
     */
    protected function prepareList(Phalcon\Mvc\Model\Resultset\Simple $news)
    {
        $data = [];
        foreach ($news as $item)
        {
            $data[] = [
                'id' => $item->id,
                'title' => $item->title,
                'content' => mb_substr($item->content, 0, 150),
                'created_at' => $item->created_at,
                'author' => [
                    'id' => $item->author->id,
                    'name' => $item->author->first_name . ' ' . $item->author->last_name
                ]
            ];
        }
        return $data;
    }

    /**
     * createAction
     *
     * @since 17.01.2020 11:12
     * @author byrkin
     * @param string param
     * @return string return
     */
    public function createAction()
    {
        try
        {
            $data = $this->request->getJsonRawBody();

            $author = $this->saveOrCreateNewsItem($data);

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
     * updateAction
     *
     * @since 17.01.2020 11:12
     * @author byrkin
     */
    public function updateAction($id)
    {
        try
        {
            $data = $this->request->getJsonRawBody();

            $this->saveOrCreateNewsItem($data, intval($id));

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
     * @since 17.01.2020 11:12
     * @author byrkin
     */
    public function deleteAction($id)
    {
        try
        {
            $item = News::findFirst(intval($id));

            if ($item)
            {
                $item->delete();
                $this->response->setJsonContent(['id' => $id]);
            }
            else
            {
                $this->setStatusCode(422, 'Unprocessable Entity');
                $this->setJsonContent(['error' => 'News item not found']);
            }
        }
        catch (\Exception $e)
        {
            $this->response->setStatusCode(500, 'Internal Server Error');
            $this->response->setJsonContent(['error' => $e->getMessage()]);
        }
    }

    /**
     * newsItemAction
     *
     * @since 17.01.2020 23:24
     * @author byrkin
     */
    public function newsItemAction($id)
    {
        $item = News::findFirst(intval($id));

        if ($item)
        {
            $this->response->setJsonContent([
                'id' => $item->id,
                'title' => $item->title,
                'content' => $item->content,
                'created_at' => $item->created_at,
                'author' => [
                    'id' => $item->author->id,
                    'name' => $item->author->first_name . ' ' . $item->author->last_name
                ]
            ]);
        }
        else
        {
            $this->setStatusCode(422, 'Unprocessable Entity');
            $this->setJsonContent(['error' => 'News item not found']);
        }
    }
}