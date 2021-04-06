<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\Workout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getWorkoutsStatistics(Request $request): JsonResponse
    {
        $user = $this->resolveDesignatedUser($request);

        return new JsonResponse([
            'missed' => $user->workoutLogs()->where('status', '=', 'missed')->count(),
            'interrupted' => $user->workoutLogs()->where('status', '=', 'interrupted')->count(),
            'completed' => $user->workoutLogs()->where('status', '=', 'completed')->count(),
        ]);
    }

    /**
     * @param Request $request
     * @param Workout $workout
     * @param int $id
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getWorkoutStatistics(Request $request, Workout $workout, int $id): JsonResponse
    {
        $user = $this->resolveDesignatedUser($request);

        /** @var Workout $workout */
        $workout = $workout->ownedBy($user->trainer_id)->findOrFail($id);

        return new JsonResponse([
            'missed' => $workout->logs()->where('user_id', '=', $user->id)->where('status', '=', 'missed')->count(),
            'interrupted' => $workout->logs()->where('user_id', '=', $user->id)->where('status', '=', 'interrupted')->count(),
            'completed' => $workout->logs()->where('user_id', '=', $user->id)->where('status', '=', 'completed')->count(),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getExercisesStatistics(Request $request): JsonResponse
    {
        $user = $this->resolveDesignatedUser($request);

        return new JsonResponse([
            'total_exercises_done' => $user->exerciseLogs()->count(),
            'total_sets_done' => $user->exerciseLogs()->sum('sets_count'),
        ]);
    }

    /**
     * @param Request $request
     * @param Exercise $exercise
     * @param int $id
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getExerciseStatistics(Request $request, Exercise $exercise, int $id): JsonResponse
    {
        $user = $this->resolveDesignatedUser($request);

        /** @var Exercise $exercise */
        $exercise = $exercise->ownedBy($user->trainer_id)->findOrFail($id);

        return new JsonResponse([
            'measurement_unit' => $exercise->measurement,
            'measurement_values' => $exercise->logs()
                ->selectRaw('ROUND(AVG(measurement_value), 2) as measurement_value, DATE(created_at) as created_at')
                ->where('user_id', '=', $user->id)
                ->groupByRaw('DATE(created_at)')
                ->get(),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getUserWeightStatistics(Request $request): JsonResponse
    {
        $user = $this->resolveDesignatedUser($request);

        return new JsonResponse([
            'data' => $user->logs()->get(['weight', 'created_at']),
        ]);
    }
}
