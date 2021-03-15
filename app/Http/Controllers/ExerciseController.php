<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExerciseController extends Controller
{
    /**
     * @var Exercise
     */
    private $exercise;

    /**
     * ExerciseController constructor.
     * @param Exercise $exercise
     */
    public function __construct(Exercise $exercise)
    {
        $this->exercise = $exercise;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $trainerId = $user->isTrainer() ? $user->id : $user->trainer_id;

        return new JsonResponse(
            $this->exercise->ownedBy($trainerId)->paginate()
        );
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function single(int $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $trainerId = $user->isTrainer() ? $user->id : $user->trainer_id;

        return new JsonResponse(
            $this->exercise->ownedBy($trainerId)->findOrFail($id)
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
        ]);

        $data['author_id'] = $request->user()->id;

        $exercise = $this->exercise->create($data);

        return new JsonResponse($exercise, JsonResponse::HTTP_CREATED);
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
        ]);

        $exercise = $this->exercise->owned()->findOrFail($id);
        $exercise->update($data);

        return new JsonResponse($exercise);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(int $id): JsonResponse
    {
        $this->exercise->owned()->findOrFail($id)->delete();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
