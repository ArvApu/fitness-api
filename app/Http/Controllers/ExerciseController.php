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
    public function all(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $trainerId = $user->isTrainer() ? $user->id : $user->trainer_id;

        return new JsonResponse(
            $this->exercise->ownedBy($trainerId)->filter($request->query())->paginate()
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
            'url' => ['sometimes', 'url', 'max:150', 'regex:/https?:\/\/(?:www\.)?youtube\.com\/watch\?v=([^&]+?)/'],
            'measurement' => ['required', 'string', 'in:seconds,minutes,grams,kilograms,quantity'],
            'description' => ['sometimes', 'string', 'max:255'],
        ], [
            'url.regex' => 'Url must be a valid Youtube url.'
        ]);

        if(isset($data['url'])) {
            $data['url'] = get_yt_embed_url($data['url']);
        }

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
            'url' => ['sometimes', 'url', 'max:150', 'regex:/https?:\/\/(?:www\.)?youtube\.com\/watch\?v=([^&]+?)/'],
            'measurement' => ['sometimes', 'string', 'in:seconds,minutes,grams,kilograms,quantity'],
            'description' => ['sometimes', 'string', 'max:255'],
        ], [
            'url.regex' => 'Url must be a valid Youtube url.'
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
