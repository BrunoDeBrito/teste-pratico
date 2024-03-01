<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{Hash};


class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $user = [
            [
                'name' => 'Administrador',
                'email' => 'admin@teste.com.br',
                'phone' => '62999990000',
                'cpf' => '99193133197',
                'role' => 2,
                'password' => Hash::make('secret'),
            ],
            [
                'name' => 'Usuário',
                'email' => 'user@teste.com.br',
                'phone' => '62999990000',
                'cpf' => '21429528109',
                'role' => 1,
                'password' => Hash::make('secret'),
            ],
        ];

        User::insert($user);

        User::factory()->count(50)->create();
    }
}
