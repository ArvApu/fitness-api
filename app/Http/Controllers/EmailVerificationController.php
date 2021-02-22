<?php

namespace App\Http\Controllers;

use App\Mail\VerifyEmail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

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
            throw new BadRequestHttpException('Bad token.');
        }

        if($data === null) {
            throw new BadRequestHttpException('Invalid token data.');
        }

        if($data->expires_at < Carbon::now()) {
            throw new BadRequestHttpException('Email verification is expired.');
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

        $token = encrypt(json_encode([
            'user_id' => $user->id,
            'email' => $user->email,
            'expires_at' => Carbon::now()->addMinutes(config('auth.email_verification_timeout')),
        ]));

        try {
            $mailer->to($user->email)->send(new VerifyEmail($token));
        } catch (\Swift_SwiftException $e) {
            throw new ServiceUnavailableHttpException(null, 'Email service unavailable.');
        }

        return new JsonResponse(['message' => 'User email verifications was resent.']);
    }
}
