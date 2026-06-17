<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Shafferouth extends Seeder
{
    public function run()
    {
        $data = [
            'client_id'     => 'TestClient',
            'client_secret' => 'test_secret'
        ];

        $this->db->table('oauth_clients')->insert($data);
    }
}