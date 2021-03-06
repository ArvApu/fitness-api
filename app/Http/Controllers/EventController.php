<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\JsonResponse;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event as CEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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

        $user = $this->resolveUser($request);

        return new JsonResponse(
            $user->events()
                ->where('start_time', '>=', $request->input('start_date'))
                ->where('start_time', '<=', $request->input('end_date'))
                ->get()
        );
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function single(Request $request, int $id): JsonResponse
    {
        $user = $this->resolveUser($request);

        return new JsonResponse(
            $user->events()->with(['attendee', 'organizer', 'workout'])->findOrFail($id)
        );
    }

    /**
     * Export events to icalendar file
     *
     * @param Request $request
     * @return Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function export(Request $request): Response
    {
        $user = $this->resolveUser($request);

        $cal = Calendar::create($user->first_name.' calendar');

        $events = $user->events()->where('end_time', '>=', Carbon::today())
            ->limit(500) // To be sure that too much data will not be retrieved
            ->get();

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
            'all_day' => ['sometimes', 'boolean'],
            'start_time' => ['required', 'date_format:Y-m-d H:i:s'],
            'end_time' => ['required', 'date_format:Y-m-d H:i:s', 'after_or_equal:start_time'],
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
     * Resolve if user is client or a trainer/admin
     *
     * @param Request $request
     * @return User
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function resolveUser(Request $request): User
    {
        /** @var User $user */
        $user = $request->user();

        if(!$user->isTrainer() && !$user->isAdmin()) {
            return $user;
        }

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
        $client = $trainer->findOrFail($clientId);

        if(!$trainer->isAdmin() && !$trainer->hasClient($client)) {
            throw new AccessDeniedHttpException('Client information is not available');
        }

        return $client;
    }
}
