<?php

namespace App\Http\Controllers\Api;

use App\Actions\Users\CreateUserAction;
use App\Actions\Users\DeleteUserAction;
use App\Actions\Users\ListUsersAction;
use App\Actions\Users\ShowUserAction;
use App\Actions\Users\UpdateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request, ListUsersAction $action): JsonResponse
    {
        $limit = (int) $request->query('limit', 20);
        $limit = $limit >= 1 && $limit <= 100 ? $limit : 20;

        return response()->json([
            'data' => $action->handle($limit),
        ]);
    }

    public function show(int $id, ShowUserAction $action): JsonResponse
    {
        return response()->json([
            'data' => $action->handle($id),
        ]);
    }

    public function store(UserStoreRequest $request, CreateUserAction $action): JsonResponse
    {
        $created = $action->handle($request->validated());

        return response()->json(['data' => $created->only(['id', 'username', 'email', 'created_at'])], 201);
    }

    public function update(int $id, UserUpdateRequest $request, UpdateUserAction $action): JsonResponse
    {
        $payload = $request->validated();
        if (empty($payload)) {
            return response()->json(['error' => 'Нечего обновлять — передайте хотя бы одно поле.'], 422);
        }

        $updated = $action->handle($id, $payload);

        return response()->json(['data' => $updated->only(['id', 'username', 'email', 'created_at'])]);
    }

    public function destroy(int $id, DeleteUserAction $action): JsonResponse
    {
        $action->handle($id);

        return response()->json(null, 204);
    }
}
