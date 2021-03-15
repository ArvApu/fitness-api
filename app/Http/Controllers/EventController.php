<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * @var Event
     */
    private $event;

    /**
     * EventController constructor.
     * @param Event $event
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        return new JsonResponse(
            $this->event->paginate()
        );
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function single(int $id): JsonResponse
    {
        return new JsonResponse(
            $this->event->findOrFail($id)
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
            'attendee_id' => ['required', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:100'],
            'information' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date_format:Y-m-d H:i:s'],
            'end_time' => ['required', 'date_format:Y-m-d H:i:s', 'after:start_time'],
        ]);

        $day = $this->event->create($data);

        return new JsonResponse($day, JsonResponse::HTTP_CREATED);
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
            'attendee_id' => ['sometimes', 'integer', 'exists:users,id'],
            'title' => ['sometimes', 'string', 'max:100'],
            'information' => ['sometimes', 'string', 'max:255'],
        ]);

        $day = $this->event->findOrFail($id);
        $day->update($data);

        return new JsonResponse($day);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(int $id): JsonResponse
    {
        $this->event->findOrFail($id)->delete();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
