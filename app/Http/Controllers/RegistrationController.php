<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmEmail;
use App\Models\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
     */
    public function register(Request $request, User $user, Hasher $hasher, Mailer $mailer): JsonResponse
    {
        $data = $this->validate($request, [
            'first_name' => ['required', 'string', 'between:3,64'],
            'last_name' => ['required', 'string', 'between:3,64'],
            'email' => ['required', 'email', 'max:64', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $data['password'] = $hasher->make($data['password']);

        /** @var User $user */
        $user = $user->create($data);

        $token = encrypt(json_encode([
            'user_id' => $user->id,
            'email' => $user->email,
            'created_at' => time(),
        ]));

        try {
             $mailer->to($data['email'])->send(new ConfirmEmail($token));
        } catch (\Swift_SwiftException $e) {
            $user->delete();
            // TODO: throw exception
        }

        return new JsonResponse(['message' => 'User successfully registered. Please login.'], JsonResponse::HTTP_CREATED);
    }
}