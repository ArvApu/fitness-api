<?php

namespace App\Http\Controllers;

use App\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NewsEventController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        return new JsonResponse(
            $user->newsEvents()->orderBy('id', 'desc')->paginate(30)
        );
    }
}
