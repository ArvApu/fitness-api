<?php

namespace App\Http\Controllers;

use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\JWTGuard;

class AuthenticationController extends Controller
{

    /**
     * @var JWTGuard
     */
    private $guard;

    /**
     * AuthenticationController constructor.
     * @param AuthManager $authManager
     */
    public function __construct(AuthManager $authManager)
    {
        $this->guard = $authManager->guard('api');
    }

    /**
     * Login to system
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $this->validate($request, [
            'email' => ['required', 'email', 'string'],
            'password' => ['required', 'string',],
        ]);

        if (! $token = $this->guard->attempt($credentials)) {
            return new JsonResponse(['error' => 'Bad credentials.'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Log out from the system
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->guard->logout();

        return new JsonResponse(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh token
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken($this->guard->refresh());
    }

    /**
     * Current authorised user information
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return new JsonResponse($this->guard->user());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     * @return JsonResponse
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return new JsonResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard->factory()->getTTL() * 60
        ]);
    }
}
