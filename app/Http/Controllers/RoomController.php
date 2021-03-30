<?php

namespace App\Http\Controllers;

use App\Http\JsonResponse;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RoomController extends Controller
{
    /**
     * @var Room
     */
    private $room;

    /**
     * RoomController constructor.
     * @param Room $room
     */
    public function __construct(Room $room)
    {
        $this->room = $room;
    }

    /**
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return new JsonResponse(
            $user->rooms()->with('users')->paginate(15)
        );
    }

    /**
     * Get all messages in room
     *
     * @param int $id
     * @return JsonResponse
     */
    public function messages(int $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        /** @var Room $room */
        $room = $user->rooms()->findOrFail($id);

        return new JsonResponse(
            $room->messages()->latest()->paginate(30)
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $data = $this->validate($request, [
            'name' => ['required', 'string', 'max:100'],
            'users' => ['required', 'array', 'min:1', 'max:10'],
            'users.*' => ['required', 'distinct', 'integer', 'exists:users,id'],
        ]);

        /** @var User $user */
        $user = $request->user();
        $users = $data['users'];

        /* If user did not add himself, lets do it automatically */
        if(!in_array($user->id, $users)) {
            $users[] = $user->id;
        }

        /* For now only 2 users can be in the room */
        if(count($users) > 2) {
            throw new BadRequestHttpException('Exceeded users in room quota.');
        }

        $hasRoom = $user->rooms()->whereHas('users', function ($query) use($users, $user) {
            $query->whereIn('user_id', array_diff($users, [$user->id]));
        })->exists();

        /* For now users can have only one room with other person */
        if($hasRoom) {
            throw new BadRequestHttpException('Room exists.');
        }

        $room = $this->room->create([
            'name' => $data['name'],
            'admin_id' => $user->id,
        ]);

        $room->users()->attach($data['users']);

        return new JsonResponse($room, JsonResponse::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $data = $this->validate($request, [
            'name' => ['sometimes', 'string', 'max:100'],
        ]);

        $room = $this->room->owned()->findOrFail($id);
        $room->update($data);

        return new JsonResponse($room);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(int $id): JsonResponse
    {
        $this->room->owned()->findOrFail($id)->delete();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * Read all messages in the room
     *
     * @param int $id
     * @return JsonResponse
     * @throws \Exception
     */
    public function readMessages(int $id): JsonResponse
    {
        /** @var Room $room */
        $room = $this->room->findOrFail($id);

        $room->messages()->where('user_id', '!=', Auth::user()->id)->update([
            'is_seen' => true
        ]);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
