<?php

namespace App\Actions\Users;

use App\Models\User;

class ShowUserAction
{
    public function handle(int $id): User
    {
        return User::query()->findOrFail($id);
    }
}
