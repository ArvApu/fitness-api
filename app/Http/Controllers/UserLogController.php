<?php

namespace App\Http\Controllers;

use App\Http\JsonResponse;
use Illuminate\Http\Request;

class UserLogController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function all(Request $request): JsonResponse
    {
        $user = $this->resolveDesignatedUser($request);
        return new JsonResponse($user->logs()->paginate());
    }
}
