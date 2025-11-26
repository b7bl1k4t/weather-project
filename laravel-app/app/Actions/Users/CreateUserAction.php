<?php

namespace App\Actions\Users;

use App\Models\User;

class CreateUserAction
{
    public function handle(array $payload): User
    {
        $user = new User($payload);
        $user->save();

        return $user;
    }
}
