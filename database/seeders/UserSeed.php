<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* factory(User::class, 1)->states('admin')->create([
            'name'  => 'Administrador',
            'email' => 'admin@teste.com.br',
            'phone' => '(62) 99999-0000',
            'cpf'   => '99193133197',
        ]);

        factory(User::class, 1)->states('user')->create([
            'name'  => 'Usuario Teste',
            'email' => 'user@teste.com.br',
            'phone' => '(62) 00000-0000',
            'cpf'   => '21429528109',
        ]);

        factory(User::class, 50)->states('user')->create(); */
    }
}
