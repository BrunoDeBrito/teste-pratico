<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return view('user');
    }

    public function info($id)
    {

        $user = User::find($id);

        return view('users.user-info', ['user' => $user]);

    }
}
