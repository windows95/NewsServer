<?php
/**
* Тестирование api новостей
*
* @since 18.01.2020 10:06
* @author byrkin
*/
class ApiNewsTest extends \PHPUnit\Framework\TestCase
{
    private $client = null;

    /**
     * setUp
     *
     * @since 18.01.2020 10:26
     * @author byrkin
     */
    public function setUp(): void
    {
        $this->client = new GuzzleHttp\Client([
            'base_uri' => 'http://news.app.local/api/',
            'timeout'  => 2.0,
            'http_errors' => false
        ]);
    }

    /**
     * getAuthorId
     *
     * @since 18.01.2020 11:45
     * @author byrkin
     * @return string|null
     */
    protected function getAuthorId()
    {
        $response = $this->client->request('POST', 'authors', [
            'body' => json_encode(['first_name' => 'Ivan', 'last_name' => 'Ivanov'])
        ]);

        if ($response->getStatusCode() != 201) {
            return null;
        }
        return json_decode($response->getBody()->getContents(), true)['id'];
    }

    /**
     * testCRUD
     *
     * @since 18.01.2020 11:17
     * @author byrkin
     */
    public function testCRUD()
    {
        $author = $this->getAuthorId();
        if (!$author) {
            return false;
        }

        // Создание новости с кривыми данными
        $response = $this->client->request('POST', 'news', [
            'body' => json_encode(['content' => '', 'title' => ''])
        ]);

        $this->assertEquals(422, $response->getStatusCode());

        // Создание новости
        $content = 'content';
        $title = 'title';

        $response = $this->client->request('POST', 'news', [
            'body' => json_encode(['author_id' => $author, 'content' => $content, 'title' => $title])
        ]);

        $this->assertEquals(201, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertTrue(is_array($data));
        $this->assertTrue(array_key_exists('id', $data));

        // Обновление созданной новости
        $id = $data['id'];

        $content = 'updated content';
        $title = 'updated title';

        $response = $this->client->request('PUT', 'news/'.$id, [
            'body' => json_encode(['content' => $content, 'title' => $title, 'author_id' => $author])
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertTrue(is_array($data));
        $this->assertTrue(array_key_exists('id', $data));

        // Просмотр новости
        $response = $this->client->request('GET', 'news/'.$id);
        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(is_array($data));
        $this->checkResponse($data);
    }

    /**
     * checkResponse
     *
     * @since 18.01.2020 11:14
     * @author byrkin
     * @param array $newsItem
     */
    protected function checkResponse(array $newsItem)
    {
        $fields = ['id', 'title', 'created_at', 'content', 'author'];
        foreach ($fields as $field) {
            $this->assertTrue(array_key_exists($field, $newsItem));
        }
        $this->assertTrue(array_key_exists('id', $newsItem['author']));
        $this->assertTrue(array_key_exists('name', $newsItem['author']));
    }

    /**
     * Проверка GET запросов
     *
     * @since 18.01.2020 10:07
     * @author byrkin
     */
    public function testGET()
    {
        // Список новостей
        $response = $this->client->request('GET', 'news');
        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json; charset=UTF-8', $response->getHeaders()['Content-Type'][0]);
        $this->assertTrue(is_array($data));
        $this->assertTrue(count($data) > 0);

        foreach ($data as $row) {
            $this->checkResponse($row);
        }
    }
}