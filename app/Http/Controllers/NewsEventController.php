<?php

namespace App\Http\Controllers;

use App\Http\JsonResponse;
use App\Models\NewsEvent;
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

        if ($user->isAdmin()) {
            $query = (new NewsEvent());
        } else {
            $query = $user->newsEvents();
        }

        return new JsonResponse($query->orderBy('id', 'desc')->paginate(10));
    }
}
