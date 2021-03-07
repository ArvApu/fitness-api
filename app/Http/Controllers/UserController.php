<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function all(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        return new JsonResponse($user->getRelatedUsers());
    }
}
