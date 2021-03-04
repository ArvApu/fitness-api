<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class MessageController extends Controller
{
    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function getByUser(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $sent = $user->sentMessages()
            ->where('receiver_id', '=', $id)
            ->orderBy('created_at')
            ->get();

        $received = $user->receivedMessages()
            ->where('sender_id', '=', $id)
            ->orderBy('created_at')
            ->get();

        $all = $sent->merge($received);

        return new JsonResponse($all->sortBy('created_at')->values());
    }

    /**
     * @param Request $request
     * @param Message $message
     * @param User $user
     * @param int $to
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function send(Request $request, Message $message, User $user, int $to): JsonResponse
    {
        $this->validate($request, [
            'message' => ['required', 'string', 'max:255'],
        ]);

        if($user->where('id', '=', $to)->doesntExist()) {
            throw new BadRequestHttpException('User that should receive message does not exist');
        }

        $message = $message->create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $to,
            'message' => $request->input('message'),
        ]);

        event(new MessageSent($message, $to));

        return new JsonResponse($message, JsonResponse::HTTP_CREATED);
    }
}
