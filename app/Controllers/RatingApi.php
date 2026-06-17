<?php
namespace App\Controllers;

use App\Models\RatingModel;
use App\Services\OAuth;
use CodeIgniter\API\ResponseTrait;

class RatingApi extends BaseController
{
    use ResponseTrait;

    protected $model;
    protected $auth;

    public function __construct()
    {
        $this->model = new RatingModel();
        $this->auth = new OAuth();
    }

    // GET / POST для получения списка
    public function rating()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        if (!$this->auth->isLoggedIn()) {
            return $this->failUnauthorized('Требуется авторизация');
        }

        $per_page = $this->request->getGet('per_page') ?? 10;
        $search   = $this->request->getGet('search') ?? '';
        $page     = (int) ($this->request->getGet('page') ?? 1);

        $data = $this->model->getRatings(null, $search, $per_page, $page);
        $pager = $this->model->pager->getDetails('group1');

        return $this->respond([
            'ratings' => $data,
            'page'    => $pager
        ]);
    }

    // Создание записи (POST)
    public function store()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        if (!$this->auth->isLoggedIn()) {
            return $this->failUnauthorized('Требуется авторизация');
        }

        if (strtolower($this->request->getMethod()) !== 'post') {
            return $this->fail('Метод не разрешён', 405);
        }

        $rules = [
            'name'        => 'required|min_length[3]|max_length[255]',
            'description' => 'required',
            'birthday'    => 'required|valid_date[Y-m-d]',
            'gender'      => 'required|in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return $this->respond([
                'status' => 'error',
                'errors' => $this->validator->getErrors(),
            ], 400);
        }

        $data = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'birthday'    => $this->request->getPost('birthday'),
            'gender'      => (int) $this->request->getPost('gender'),
            'user_id'     => 1,
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        if ($this->model->save($data)) {
            return $this->respondCreated(['message' => 'Rating created successfully']);
        } else {
            return $this->fail('Ошибка сохранения', 500);
        }
    }

    // Обновление записи (POST /RatingApi/update/{id})
    public function update($id = null)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        if (!$this->auth->isLoggedIn()) {
            return $this->failUnauthorized('Требуется авторизация');
        }

        if (!$id) {
            return $this->fail('ID не указан', 400);
        }

        if (!$this->model->find($id)) {
            return $this->failNotFound('Запись не найдена');
        }

        $rules = [
            'name'        => 'required|min_length[3]|max_length[255]',
            'description' => 'required',
            'birthday'    => 'required|valid_date[Y-m-d]',
            'gender'      => 'required|in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return $this->respond([
                'status' => 'error',
                'errors' => $this->validator->getErrors(),
            ], 400);
        }

        $data = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'birthday'    => $this->request->getPost('birthday'),
            'gender'      => (int) $this->request->getPost('gender'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        if ($this->model->update($id, $data)) {
            return $this->respond(['message' => 'Rating updated successfully'], 200);
        } else {
            return $this->fail('Ошибка обновления', 500);
        }
    }

    // Удаление записи (DELETE)
    public function delete($id = null)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        if (!$this->auth->isLoggedIn()) {
            return $this->failUnauthorized('Требуется авторизация');
        }

        if (!$id) {
            return $this->fail('ID не указан', 400);
        }

        $existing = $this->model->find($id);
        if (!$existing) {
            return $this->failNotFound('Запись не найдена');
        }

        if ($this->model->delete($id)) {
            return $this->respondDeleted(['message' => 'Rating deleted successfully']);
        } else {
            return $this->fail('Ошибка удаления', 500);
        }
    }
}