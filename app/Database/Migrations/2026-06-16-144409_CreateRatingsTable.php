<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRatingsTable extends Migration
{
    public function up()
    {
        // Добавляем поля таблицы
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'birthday' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'gender' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // Устанавливаем первичный ключ
        $this->forge->addKey('id', true);

        // Создаём таблицу
        $this->forge->createTable('ratings');
    }

    public function down()
    {
        // Удаляем таблицу при откате
        $this->forge->dropTable('ratings');
    }
}