<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\Room;
use App\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * @param Request $request
     * @param Room $room
     * @param int $roomId
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function send(Request $request, Room $room, int $roomId): JsonResponse
    {
        $this->validate($request, [
            'message' => ['required', 'string', 'max:255'],
        ]);

        /** @var Room $room */
        $room = $room->findOrFail($roomId);

        /** @var Message $message */
        $message = $room->messages()->create([
            'user_id' => $request->user()->id,
            'message' => $request->input('message'),
        ]);

        $sender = $request->user();

        foreach ($room->users as $user) {
            if ($user->id === $sender->id) {
                continue;
            }
            event(new MessageSent($message, $user->id));
        }

        return new JsonResponse($message, JsonResponse::HTTP_CREATED);
    }
}
