<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function all(Request $request, User $user): JsonResponse
    {
        return new JsonResponse(
            $user->where('id', '!=', $request->user()->id)
                ->get()
        );
    }
}
