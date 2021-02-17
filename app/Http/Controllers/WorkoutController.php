<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkoutController extends Controller
{
    /**
     * @var Workout
     */
    private $workout;

    /**
     * WorkoutController constructor.
     * @param Workout $workout
     */
    public function __construct(Workout $workout)
    {
        $this->workout = $workout;
    }

    /**
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        return new JsonResponse(
            $this->workout->get()
        );
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function single(int $id): JsonResponse
    {
        return new JsonResponse(
            $this->workout->findOrFail($id)
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
            'description' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:weight,cardio,hiit,recovery,general'],
            'is_private' => ['required', 'boolean'],
        ]);

        $data['author_id'] = $request->user()->id;

        $workout = $this->workout->create($data);

        return new JsonResponse($workout, JsonResponse::HTTP_CREATED);
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
            'description' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'in:weight,cardio,hiit,recovery,general'],
            'is_private' => ['sometimes', 'boolean'],
        ]);

        $workout = $this->workout->findOrFail($id);
        $workout->update($data);

        return new JsonResponse($workout);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(int $id): JsonResponse
    {
        $this->workout->findOrFail($id)->delete();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
