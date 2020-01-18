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
     * Соответствие названиий параметров и полей модели
     * @var array
     */
    protected $map = ['title' => 'title', 'content' => 'content', 'author_id' => 'author_id'];

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
        foreach ($news as $newsItem)
        {
            $data[] = [
                'id' => $newsItem->id,
                'title' => $newsItem->title,
                'content' => mb_substr($newsItem->content, 0, 150),
                'created_at' => $newsItem->created_at,
                'author' => [
                    'id' => $newsItem->author->id,
                    'name' => $newsItem->author->first_name . ' ' . $newsItem->author->last_name
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
     */
    public function createAction()
    {
        try
        {
            $newsItem = new News();
            $this->saveOrCreateRecord($newsItem, $this->map, $this->request->getJsonRawBody());

            $this->response->setStatusCode(201, 'Created');
            $this->response->setJsonContent(['id' => $newsItem->id]);
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
            $newsItem = News::findFirst(intval($id));
            if (!$newsItem) {
                throw new WrongDataException('News item not found');
            }
            $this->saveOrCreateRecord($newsItem, $this->map, $this->request->getJsonRawBody());
            $this->response->setJsonContent(['id' => $newsItem->id]);
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
            $newsItem = News::findFirst(intval($id));

            if ($newsItem)
            {
                $newsItem->delete();
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
        $newsItem = News::findFirst(intval($id));

        if ($newsItem)
        {
            $this->response->setJsonContent([
                'id' => $newsItem->id,
                'title' => $newsItem->title,
                'content' => $newsItem->content,
                'created_at' => $newsItem->created_at,
                'author' => [
                    'id' => $newsItem->author->id,
                    'name' => $newsItem->author->first_name . ' ' . $newsItem->author->last_name
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