<?php

namespace App\Http\Controllers;

use App\Events\UserProfileUpdated;
use App\Models\User;
use App\Http\JsonResponse;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Update user's profile information
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request): JsonResponse
    {
        $data = $this->validate($request, [
            'first_name' => ['sometimes', 'string', 'between:3,64'],
            'last_name' => ['sometimes', 'string', 'between:3,64'],
            'birthday' => ['sometimes', 'date', 'before:today'],
            'weight' => ['sometimes', 'numeric', 'between:0,500'],
            'height' => ['sometimes', 'integer', 'between:0,300'],
            'experience' => ['sometimes', 'integer', 'between:0,100'],
            'about' => ['sometimes', 'string', 'max:250'],
        ]);

        /** @var User $user */
        $user = $request->user();

        $oldUser = $user->replicate();
        $user->update($data);

        event(new UserProfileUpdated($user, $oldUser));

        return new JsonResponse($user);
    }

    /**
     * Change user password
     *
     * @param Request $request
     * @param Hasher $hasher
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function changePassword(Request $request, Hasher $hasher): JsonResponse
    {
        $this->validate($request, [
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        /** @var User $user */
        $user = $request->user();

        $user->update([
            'password' => $hasher->make($request->input('new_password')),
        ]);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
