<?php

namespace App\Http\Controllers;

use App\Http\JsonResponse;
use App\Models\WorkoutLog;
use Illuminate\Http\Request;

class WorkoutLogController extends Controller
{
    /**
     * @var WorkoutLog
     */
    private $workoutLog;

    /**
     * WorkoutController constructor.
     * @param WorkoutLog $workoutLog
     */
    public function __construct(WorkoutLog $workoutLog)
    {
        $this->workoutLog = $workoutLog;
    }

    /**
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        return new JsonResponse(
            $this->workoutLog->paginate()
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
            'workout_id' => ['required', 'integer', 'exists:workouts,id', 'max:100'],
            'status' => ['required', 'string', 'in:missed,interrupted,completed'],
            'comment' => ['sometimes', 'string', 'max:100'],
            'difficulty' => ['required', 'string', 'in:easy,moderate,hard,exhausting'],
        ]);

        $data['user_id'] = $request->user()->id;

        $workout = $this->workoutLog->create($data);

        return new JsonResponse($workout, JsonResponse::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(int $id): JsonResponse
    {
        $this->workoutLog->findOrFail($id)->delete();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
