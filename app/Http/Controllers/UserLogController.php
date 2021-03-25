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
        // TODO: protect for user's/his trainer/admin eys only
        return new JsonResponse(
            $userLog->paginate()
        );
    }
}
