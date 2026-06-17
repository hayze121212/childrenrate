<?php

namespace App\Models;

use CodeIgniter\Model;

class RatingModel extends Model
{
    protected $table            = 'ratings';        // Название вашей таблицы
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['user_id', 'name', 'description', 'birthday', 'gender']; // поля как в методичке

    /**
     * Получить рейтинги с пагинацией и поиском
     *
     * @param int|null $user_id   ID пользователя (null – все записи)
     * @param string   $search    Поиск по полю 'name'
     * @param int|null $per_page  Количество на странице
     * @return mixed
     */
    public function getRatings($user_id = null, $search = '', $per_page = null, $page = null)
    {
        $model = $this;

        if (!empty($search)) {
            $model = $model->like('name', $search, 'both');
        }

        if (!is_null($user_id)) {
            $model = $model->where('user_id', $user_id);
        }

        // Передаём номер страницы явно, чтобы работал параметр ?page (а не только page_group1)
        return $model->paginate($per_page, 'group1', $page);
    }
}