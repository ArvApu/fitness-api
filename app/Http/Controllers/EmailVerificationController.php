<?php

namespace App\Http\Controllers;

use App\Mail\VerifyEmail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        return new JsonResponse(['message' => 'User email is verified.']);
    }

    /**
     * Resend email verification for current user
     *
     * @param Request $request
     * @param Mailer $mailer
     * @return JsonResponse
     */
    public function resend(Request $request, Mailer $mailer): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if($user === null) {
            return new JsonResponse(['error' => 'Unauthorized.'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $token = encrypt(json_encode([
            'user_id' => $user->id,
            'email' => $user->email,
            'expires_at' => Carbon::now()->addMinutes(60), // TODO: set expiration via configuration
        ]));

        try {
            $mailer->to($user->email)->send(new VerifyEmail($token));
        } catch (\Swift_SwiftException $e) {
            return new JsonResponse(['error' => 'Email service unavailable.'], JsonResponse::HTTP_SERVICE_UNAVAILABLE);
        }

        return new JsonResponse(['message' => 'User email verifications was resent.']);
    }
}
