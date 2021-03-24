<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\JsonResponse;
use Illuminate\Http\Request;
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

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $data = $this->validate($request, [
            'email' => ['sometimes', 'email', 'unique:users,email,'.$id],
        ]);

        // TODO: send verification email

        $user = (new User())->findOrFail($id);

        $user->update($data);

        return new JsonResponse($user);
    }
}
