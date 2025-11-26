<?php

namespace App\Actions\Uploads;

use App\Models\Upload;
use Illuminate\Support\Collection;

class ListUploadsAction
{
    public function handle(int $limit = 50): Collection
    {
        $limit = max(1, min($limit, 200));

        return Upload::query()
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
