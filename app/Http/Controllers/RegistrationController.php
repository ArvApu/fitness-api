<?php

namespace App\Http\Controllers;

use App\Mail\VerifyEmail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Mail\Mailer;
use App\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class RegistrationController extends Controller
{
    /**
     * Register to the system
     *
     * @param Request $request
     * @param User $user
     * @param Hasher $hasher
     * @param Mailer $mailer
     * @return JsonResponse
     * @throws ValidationException
     * @throws \Exception
     */
    public function register(Request $request, User $user, Hasher $hasher, Mailer $mailer): JsonResponse
    {
        $data = $this->validate($request, [
            'first_name' => ['required', 'string', 'between:3,64'],
            'last_name' => ['required', 'string', 'between:3,64'],
            'email' => ['required', 'email', 'max:64', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'token' => ['sometimes', 'string']
        ]);

        $this->resolveInvitation($data['token'] ?? null, $data);
        $data['password'] = $hasher->make($data['password']);

        /** @var User $user */
        $user = $user->create($data);

        $token = encrypt(json_encode([
            'user_id' => $user->id,
            'email' => $user->email,
            'expires_at' => Carbon::now()->addMinutes(config('auth.email_verification_timeout'))->toDateTimeString(),
        ]));

        try {
            $mailer->to($data['email'])->send(new VerifyEmail($token));
        } catch (\Swift_SwiftException $e) {
            $user->delete();
            throw new ServiceUnavailableHttpException(null ,'Registration is unavailable.');
        }

        return new JsonResponse(['message' => 'User successfully registered. Please login.'], JsonResponse::HTTP_CREATED);
    }

    /**
     * Resolve if user was invited
     *
     * @param string|null $token
     * @param array $data
     */
    protected function resolveInvitation(?string $token, array &$data): void
    {
        if($token === null) {
            $data['role'] = 'trainer';
            return;
        }

        try {
            $tokenData = json_decode(decrypt($token));
        } catch (DecryptException $exception) {
            throw new BadRequestHttpException('Bad token.');
        }

        if(!isset($tokenData->trainer_id) || !isset($tokenData->for)) {
            throw new BadRequestHttpException('Invalid token data.');
        }

        if($data['email'] !== $tokenData->for) {
            throw new BadRequestHttpException('Invitation is intended for other user.');
        }

        unset($data['token']);
        $data['role'] = 'user';
        $data['trainer_id'] = $tokenData->trainer_id;
    }
}
