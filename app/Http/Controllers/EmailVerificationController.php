<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\JsonResponse;

class EmailVerificationController extends Controller
{
    /**
     * Verify email
     *
     * @param User $user
     * @param string $token
     * @return JsonResponse
     */
    public function verify(User $user, string $token): JsonResponse
    {
        try {
            $data = json_decode(decrypt($token));  // TODO: maybe token data should be in object, object constructor must resolve decryption and data.
        } catch (DecryptException $exception) {
            return new JsonResponse(['error' => 'Bad token.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if($data === null) {
            return new JsonResponse(['error' => 'Invalid token data.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if($data->expires_at < Carbon::now()) {
            return new JsonResponse(['error' => 'Email verification is expired.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user->where('email', '=', $data->email)->update([
            'email_verified_at' => Carbon::now()
        ]);

        return new JsonResponse(['message' => 'User email is verified.'], JsonResponse::HTTP_CREATED);
    }
}
