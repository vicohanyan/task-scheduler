<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserIndexRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Toggle user availability
     * PATCH /users/{user}/availability
     * @param User $user
     * @return JsonResponse
     */
    public function toggle(User $user): JsonResponse
    {
        $user->available = ! $user->available;
        $user->save();

        return response()->json([
            'id'        => $user->id,
            'available' => $user->available,
        ]);
    }

    /**
     * Get users (with optional available filter)
     * GET /users?available=1
     * @param UserIndexRequest $request
     * @return Collection
     */
    public function index(UserIndexRequest $request): Collection
    {
        $data = $request->validated();

        $query = User::query();
        if (array_key_exists('available', $data) && $data['available'] !== null) {
            $query->where('available', (bool) $data['available']);
        }
        return $query->get();
    }
}
