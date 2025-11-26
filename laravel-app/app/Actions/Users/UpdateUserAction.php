<?php

namespace App\Actions\Users;

use App\Models\User;

class UpdateUserAction
{
    public function handle(int $id, array $payload): User
    {
        $user = User::query()->findOrFail($id);
        $user->fill($payload);
        $user->save();

        return $user;
    }
}
