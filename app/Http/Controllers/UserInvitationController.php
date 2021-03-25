<?php

namespace App\Http\Controllers;

use App\Mail\InviteUser;
use App\Models\User;
use App\Http\JsonResponse;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailer;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class UserInvitationController extends Controller
{
    /**
     * @param Request $request
     * @param Mailer $mailer
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function invite(Request $request, Mailer $mailer): JsonResponse
    {
        $data = $this->validate($request, [
            'email' => ['required', 'email']
        ]);

        /** @var User $user */
        $user = $request->user();

        $token = encrypt(json_encode([
            'trainer_id' => $user->id,
            'for' => $data['email'],
        ]));

        $inviter = $user->full_name;
        $exists = $user->where('email', '=', $data['email'])->exists();

        try {
            $mailer->to($data['email'])->send(new InviteUser($token, $inviter, $exists));
        } catch (\Swift_SwiftException $e) {
            throw new ServiceUnavailableHttpException(null ,'Can not invite user at this moment.');
        }

        return new JsonResponse(['message' => 'Invitation sent.']);
    }

    /**
     * @param string $token
     * @param User $user
     * @return JsonResponse
     */
    public function confirm(string $token, User $user): JsonResponse
    {
        try {
            $tokenData = json_decode(decrypt($token));
        } catch (DecryptException $exception) {
            throw new BadRequestHttpException('Bad token.');
        }

        if(!isset($tokenData->trainer_id) || !isset($tokenData->for)) {
            throw new BadRequestHttpException('Invalid token data.');
        }

        $user->where('email', '=', $tokenData->for)->update([
            'trainer_id' => $tokenData->trainer_id,
        ]);

        return new JsonResponse(['message' => 'Success.']);
    }
}
