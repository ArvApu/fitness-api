<?php

namespace App\Http\Controllers;

use App\Mail\ResetPassword;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    /**
     * Send password reset
     *
     * @param Request $request
     * @param PasswordReset $passwordReset
     * @param Mailer $mailer
     * @return JsonResponse
     * @throws ValidationException
     */
    public function send(Request $request, PasswordReset $passwordReset, Mailer $mailer): JsonResponse
    {
        $this->validate($request, [
           'email' => ['required', 'email']
        ]);

        $token = Str::random(32);
        $expiration = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        $passwordReset->updateOrCreate(['email' => $request->input('email')], [
            'token' => hash('sha256', $token),
            'expires_at' => Carbon::now()->addMinutes($expiration),
        ]);

        try {
            $mailer->to($request->input('email'))->send(new ResetPassword($token, $expiration));
        } catch (\Swift_SwiftException $e) {
            return new JsonResponse(['error' => 'Password reset cannot be send at this moment.'], JsonResponse::HTTP_SERVICE_UNAVAILABLE);
        }

        // TODO: Add command that deletes expired password resets

        return new JsonResponse(['message' => 'Email confirmation sent.']);
    }

    /**
     * Change password using password reset
     *
     * @param Request $request
     * @param PasswordReset $passwordReset
     * @param User $user
     * @param Hasher $hasher
     * @param string $token
     * @return JsonResponse
     * @throws ValidationException
     */
    public function reset(Request $request, PasswordReset $passwordReset, User $user, Hasher $hasher, string $token): JsonResponse
    {
        $this->validate($request, [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $passwordReset = $passwordReset->where('token', '=', hash('sha256', $token))
            ->where('expires_at', '>', Carbon::now())->first();

        if($passwordReset == null) {
            return new JsonResponse(['error' => 'Password reset is expired.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user->where('email', '=', $passwordReset->email)->update([
            'password' => $hasher->make($request->input('password')),
        ]);

        $passwordReset->delete(); // TODO: make transaction (user update and password reset delete is atomic action)

        return new JsonResponse(['message' => 'Password changed.']);
    }
}
