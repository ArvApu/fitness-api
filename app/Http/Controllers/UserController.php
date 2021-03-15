<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        return new JsonResponse($user->getRelatedUsers()->paginate());
    }
}
