<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Routing\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

abstract class Controller extends BaseController
{
    /**
     * Get simple role user (client) either if he is authenticated or admin/trainer is trying to get it
     *
     * @param Request $request
     * @return User
     * @throws ValidationException
     */
    protected function resolveDesignatedUser(Request $request): User
    {
        /** @var User $user */
        $user = $request->user();

        if (!$user->isTrainer() && !$user->isAdmin()) {
            return $user; // Currently authenticated user is client (simple user role)
        }

        /* Admin or trainer must provide existing user's id */
        $this->validate($request, [
            'user_id' => ['required', 'exists:users,id']
        ]);

        return $this->getClient($request->input('user_id'), $user);
    }

    /**
     * Get trainer's client
     *
     * @param int $clientId
     * @param User $trainer
     * @return User
     */
    protected function getClient(int $clientId, User $trainer): User
    {
        /** @var User $client */
        $client = $trainer->findOrFail($clientId); // Bit hacky but reusing trainer's user model to fetch client's instance

        /* Non admin users can only access their clients only */
        if (!$trainer->isAdmin() && !$trainer->hasClient($client)) {
            throw new AccessDeniedHttpException('Client information is not available');
        }

        return $client;
    }
}
