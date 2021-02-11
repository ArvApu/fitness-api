<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    /**
     * Login to system
     *
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function login(Request $request): Response
    {
        $data = $this->validate($request, [
            'email' => ['required', 'email', 'string'],
            'password' => ['required', 'string',],
        ]);

        // TODO: AUTH logic

        $token = 'TODO: GIVE NORMAL JWT';

        return response(['token' => $token], 200);
    }
}
