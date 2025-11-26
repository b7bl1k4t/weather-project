<?php

namespace App\Actions\Users;

use App\Models\User;

class DeleteUserAction
{
    public function handle(int $id): void
    {
        $user = User::query()->findOrFail($id);
        $user->delete();
    }
}
