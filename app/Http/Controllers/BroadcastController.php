<?php

namespace App\Http\Controllers;

use App\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;

class BroadcastController extends Controller
{
    /**
     * Authenticate the request for channel access.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function authenticate(Request $request): JsonResponse
    {
        return new JsonResponse(
            Broadcast::auth($request)
        );
    }
}
