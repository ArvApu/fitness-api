<?php

namespace App\Http\Controllers;

use App\Mail\ResetPassword;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Mail\Mailer;
use App\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

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
        $expiration = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');

        $passwordReset->updateOrCreate(['email' => $request->input('email')], [
            'token' => hash('sha256', $token),
            'expires_at' => Carbon::now()->addMinutes($expiration),
        ]);

        try {
            $mailer->to($request->input('email'))->send(new ResetPassword($token, $expiration));
        } catch (\Swift_SwiftException $e) {
            throw new ServiceUnavailableHttpException(null, 'Password reset cannot be send at this moment.');
        }

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

        if ($passwordReset == null) {
            throw new BadRequestHttpException('Password reset is expired.');
        }

        $password = $hasher->make($request->input('password'));

        DB::transaction(function () use ($user, $passwordReset, $password) {
            $user->where('email', '=', $passwordReset->email)->update([
                'password' => $password,
            ]);

            $passwordReset->delete();
        });

        return new JsonResponse(['message' => 'Password changed.']);
    }
}
