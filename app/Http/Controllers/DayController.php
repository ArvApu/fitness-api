<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Http\JsonResponse;
use Illuminate\Http\Request;

class DayController extends Controller
{
    /**
     * @var Day
     */
    private $day;

    /**
     * DayController constructor.
     * @param Day $day
     */
    public function __construct(Day $day)
    {
        $this->day = $day;
    }

    /**
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        return new JsonResponse(
            $this->day->paginate()
        );
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function single(int $id): JsonResponse
    {
        return new JsonResponse(
            $this->day->findOrFail($id)
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
            'title' => ['required', 'string', 'max:100'],
            'information' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
        ]);

        $day = $this->day->create($data);

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
            'title' => ['sometimes', 'string', 'max:100'],
            'information' => ['sometimes', 'string', 'max:255'],
            'date' => ['sometimes', 'date'],
        ]);

        $day = $this->day->findOrFail($id);
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
        $this->day->findOrFail($id)->delete();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
