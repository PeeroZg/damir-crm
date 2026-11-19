<?php

namespace App\Http\Controllers;

use App\Enums\ActivityType;
use App\Http\Requests\ActivityRequest;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function store(ActivityRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $subject = Relation::getMorphedModel($data['subject_type'])::findOrFail($data['subject_id']);
        $isTask = $data['type'] === ActivityType::Task->value;

        $activity = new Activity([
            'type' => $data['type'],
            'title' => $data['title'] ?? null,
            'body' => $data['body'] ?? null,
            'due_at' => $isTask ? ($data['due_at'] ?? null) : null,
            'assigned_to' => $isTask ? ($data['assigned_to'] ?? $request->user()->id) : null,
        ]);
        $activity->author()->associate($request->user());
        $subject->activities()->save($activity);

        return back()->with('success', $isTask ? __('Task added.') : __('Activity logged.'));
    }

    public function toggle(Request $request, Activity $activity): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $activity);

        $activity->update(['completed_at' => $activity->completed_at ? null : now()]);

        $message = $activity->completed_at ? __('Task completed. Nice!') : __('Task reopened.');

        return $request->expectsJson()
            ? response()->json(['message' => $message, 'completed' => $activity->completed_at !== null])
            : back()->with('success', $message);
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        $this->authorize('delete', $activity);

        $activity->delete();

        return back()->with('success', __('Activity deleted.'));
    }
}
