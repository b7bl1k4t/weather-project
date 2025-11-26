<?php

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Collection;

class ListUsersAction
{
    public function handle(int $limit = 20): Collection
    {
        $limit = max(1, min($limit, 100));

        return User::query()
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get(['id', 'username', 'email', 'created_at']);
    }
}
