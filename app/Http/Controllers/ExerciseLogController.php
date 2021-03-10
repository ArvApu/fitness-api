<?php

namespace App\Http\Controllers;

use App\Http\JsonResponse;
use App\Models\ExerciseLog;

class ExerciseLogController extends Controller
{
    /**
     * @var ExerciseLog
     */
    private $exerciseLog;

    /**
     * WorkoutController constructor.
     * @param ExerciseLog $exerciseLog
     */
    public function __construct(ExerciseLog $exerciseLog)
    {
        $this->exerciseLog = $exerciseLog;
    }

    /**
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        return new JsonResponse(
            $this->exerciseLog->paginate()
        );
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function single(int $id): JsonResponse
    {
        return new JsonResponse(
            $this->exerciseLog->findOrFail($id)
        );
    }
}
