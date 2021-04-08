<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use App\Http\JsonResponse;
use App\Models\WorkoutExercise;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

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
     * @param Request $request
     * @return JsonResponse
     */
    public function all(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $trainerId = $user->isTrainer() ? $user->id : $user->trainer_id;

        return new JsonResponse(
            $this->workout->ownedBy($trainerId)->filter($request->query())->paginate()
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
            $this->workout->ownedBy($trainerId)->with('exercises')->findOrFail($id)
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
        ]);

        $data['author_id'] = $request->user()->id;

        $workout = $this->workout->create($data);

        return new JsonResponse($workout, JsonResponse::HTTP_CREATED);
    }

    /**
     * Assign exercises to a workout
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function assignExercises(Request $request, int $id): JsonResponse
    {
        /** @var Workout $workout */
        $workout = $this->workout->owned()->findOrFail($id);

        $data = $this->validate($request, [
            'exercises' => ['required', 'array', 'max:' . Workout::MAX_NUMBER_OF_EXERCISES_ASSIGNED],
            'exercises.*.id' => ['required', 'integer', 'distinct', 'exists:exercises'],
            'exercises.*.order' => ['required', 'integer', 'min:1', 'max:65000', 'distinct'],
            'exercises.*.reps' => ['required', 'integer', 'min:1', 'max:65000'],
            'exercises.*.sets' => ['required', 'integer', 'min:1', 'max:65000'],
            'exercises.*.rest' => ['required', 'integer', 'min:0', 'max:65000'],
        ]);

        $exercises = new Collection($data['exercises']);

        if ($workout->exercises()->count() + $exercises->count() > Workout::MAX_NUMBER_OF_EXERCISES_ASSIGNED) {
            throw new ConflictHttpException('Workout can only have ' . Workout::MAX_NUMBER_OF_EXERCISES_ASSIGNED . ' exercises assigned.');
        }

        if ($workout->exercises()->whereIn('order', $exercises->pluck('order'))->exists()) {
            throw new ConflictHttpException('Exercises have order conflict.');
        }

        $keyed = $exercises->mapWithKeys(function ($exercise) {
            return [take($exercise, 'id') => $exercise];
        });

        $workout->exercises()->attach($keyed);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * Copy a workout
     *
     * @param int $id
     * @return JsonResponse
     */
    public function copy(int $id): JsonResponse
    {
        /** @var Workout $workout */
        $workout = $this->workout->owned()->with('exercises')->findOrFail($id);
        return new JsonResponse($workout->copy(), JsonResponse::HTTP_CREATED);
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
        ]);

        $workout = $this->workout->owned()->findOrFail($id);
        $workout->update($data);

        return new JsonResponse($workout);
    }

    /**
     * Reassign exercises to a workout
     *
     * @param Request $request
     * @param WorkoutExercise $workoutExercise
     * @param int $id
     * @param int $assignedId
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function reassignExercises(Request $request, WorkoutExercise $workoutExercise, int $id, int $assignedId): JsonResponse
    {
        /** @var Workout $workout */
        $workout = $this->workout->owned()->findOrFail($id);

        $data = $this->validate($request, [
            'order' => ['sometimes', 'integer', 'min:1', 'max:65000'],
            'reps' => ['sometimes', 'integer', 'min:1', 'max:65000'],
            'sets' => ['sometimes', 'integer', 'min:1', 'max:65000'],
            'rest' => ['sometimes', 'integer', 'min:0', 'max:65000'],
        ]);

        if(empty($data)) {
            return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
        }

        $hasOrderConflicts = isset($data['order']) && $workoutExercise
            ->where('workout_id', '=', $workout->id)
            ->where('id','!=', $assignedId)
            ->where('order', '=', $data['order'])
            ->exists();

        if ($hasOrderConflicts) {
            throw new ConflictHttpException('Exercise have order conflict.');
        }

        $workoutExercise = $workoutExercise
            ->where('workout_id', '=', $workout->id)
            ->where('id','=', $assignedId)
            ->firstOrFail();

        $workoutExercise->update($data);

        return new JsonResponse($workoutExercise);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(int $id): JsonResponse
    {
        $this->workout->owned()->findOrFail($id)->delete();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * Unassign exercise from a workout
     *
     * @param WorkoutExercise $workoutExercise
     * @param int $id
     * @param int $assignedId
     * @return JsonResponse
     */
    public function unassignExercise(WorkoutExercise $workoutExercise, int $id, int $assignedId): JsonResponse
    {
        /** @var Workout $workout */
        $workout = $this->workout->owned()->findOrFail($id);

        $workoutExercise
            ->where('workout_id', '=', $workout->id)
            ->where('id','=', $assignedId)
            ->delete();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
