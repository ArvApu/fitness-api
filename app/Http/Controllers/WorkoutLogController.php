<?php

namespace App\Http\Controllers;

use App\Events\WorkoutLogged;
use App\Http\JsonResponse;
use App\Models\Workout;
use App\Models\WorkoutLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     * @param int $id
     * @return JsonResponse
     */
    public function single(int $id): JsonResponse
    {
        return new JsonResponse(
            $this->workoutLog->with('exerciseLogs')->findOrFail($id)
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            'workout_id' => ['required', 'integer', 'exists:workouts,id'],
            'status' => ['required', 'string', 'in:missed,interrupted,completed'],
            'comment' => ['sometimes', 'string', 'max:100'],
            'difficulty' => ['required', 'string', 'in:easy,moderate,hard,exhausting'],
            'exercise_logs' => ['sometimes', 'array', 'max:' . Workout::MAX_NUMBER_OF_EXERCISES_ASSIGNED],
            'exercise_logs.*.exercise_id' => ['required', 'integer', 'distinct', 'exists:exercises,id'],
            'exercise_logs.*.measurement_value' => ['required', 'numeric', 'min:0', 'max:100000'],
            'exercise_logs.*.sets_count' => ['required', 'integer', 'min:1', 'max:65000'],
            'exercise_logs.*.sets_done' => ['required', 'integer', 'min:1', 'lte:exercise_logs.*.sets_count', 'max:65000'],
            'log_date' => ['sometimes', 'date', 'before:tomorrow'],
        ]);

        $logDate = $request->input('log_date', Carbon::now()->toDateTimeString());

        /** @var WorkoutLog $log */
        $log = DB::transaction(function () use ($request, $logDate) {

            $user = $this->resolveDesignatedUser($request);

            $exerciseLogs = $request->input('exercise_logs', []);

            foreach ($exerciseLogs as &$exerciseLog) {
                $exerciseLog['user_id']  = $user->id;
                $exerciseLog['log_date'] = $logDate;
            }

            /** @var WorkoutLog $log */
            $log = $user->workoutLogs()->create(
                $request->only(['workout_id', 'status', 'comment', 'difficulty']) +
                ['log_date' => $logDate]
            );

            $log->exerciseLogs()->createMany($exerciseLogs);

            return $log;
        });

        event(new WorkoutLogged($log->replicate())); // Replicating, because otherwise eloquent returns log with used relationships

        return new JsonResponse($log, JsonResponse::HTTP_CREATED);
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
