<?php

namespace App\Http\Controllers;

use App\Mail\ResetPassword;
use App\Models\PasswordReset;
use Carbon\Carbon;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Send password reset
     *
     * @param Request $request
     * @param PasswordReset $passwordReset
     * @param Hasher $hasher
     * @param Mailer $mailer
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function reset(Request $request, PasswordReset $passwordReset, Hasher $hasher, Mailer $mailer): JsonResponse
    {
        $this->validate($request, [
           'email' => ['required', 'email']
        ]);

        $token = Str::random(32);
        $expiration = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        $passwordReset->updateOrCreate(['email' => $request->input('email')], [
            'token' => $hasher->make($token),
            'expires_at' => Carbon::now()->addMinutes(),
        ]);

        try {
            $mailer->to($request->input('email'))->send(new ResetPassword($token, $expiration));
        } catch (\Swift_SwiftException $e) {
            // TODO: throw exception
        }

        // TODO: Add command that deletes expired password resets

        return new JsonResponse(['message' => 'Email confirmation sent.']);
    }

}
