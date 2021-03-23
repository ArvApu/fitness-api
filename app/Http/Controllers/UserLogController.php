<?php

namespace App\Http\Controllers;

use App\Http\JsonResponse;
use App\Models\UserLog;

class UserLogController extends Controller
{
    /**
     * @param UserLog $userLog
     * @return JsonResponse
     */
    public function all(UserLog $userLog): JsonResponse
    {
        return new JsonResponse(
            $userLog->paginate()
        );
    }
}
