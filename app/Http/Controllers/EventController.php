<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\JsonResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event as CEvent;

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
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function all(Request $request): JsonResponse
    {
        $this->validate($request, [
            'start_date' => ['required', 'before:end_date', 'date'],
            'end_date' => ['required', 'date'],
        ]);

        return new JsonResponse(
            $this->event
                ->where('start_time', '>=', $request->input('start_date'))
                ->where('start_time', '<=', $request->input('end_date'))
                ->get()
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

    /**
     * Export events to icalendar file
     *
     * @return Response
     */
    public function export(): Response
    {
        /** @var User $user */
        $user = Auth::user();

        $cal = Calendar::create($user->first_name.' calendar');

        $events = $this->event->getFromThisDay();

        /** @var Event $event */
        foreach ($events as $event) {
            $cal->event(
                CEvent::create()
                    ->name($event->title)
                    ->description($event->information)
                    ->createdAt($event->created_at)
                    ->startsAt($event->start_time)
                    ->endsAt($event->end_time)
                    ->transparent()
            );
        }

        return new Response($cal->get(), 200, [
            'Content-Type' => 'text/calendar',
            'charset' => 'utf-8',
            'Content-Disposition' => 'attachment; filename="calendar.ics"',
        ]);
    }
}
