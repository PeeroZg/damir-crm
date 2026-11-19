<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $filter = in_array($request->query('filter'), ['open', 'overdue', 'done'], true) ? $request->query('filter') : 'open';
        $assignee = $user->isAdmin() && $request->query('user') === 'all' ? null : $user->id;

        $base = Activity::query()->tasks()->when($assignee, fn (Builder $q) => $q->where('assigned_to', $assignee));

        $tasks = (clone $base)
            ->with(['subject', 'assignee', 'author'])
            ->when($filter === 'open', fn (Builder $q) => $q->open()->orderByRaw('due_at IS NULL')->orderBy('due_at'))
            ->when($filter === 'overdue', fn (Builder $q) => $q->overdue()->orderBy('due_at'))
            ->when($filter === 'done', fn (Builder $q) => $q->whereNotNull('completed_at')->latest('completed_at'))
            ->paginate(20)
            ->withQueryString();

        return view('tasks.index', [
            'tasks' => $tasks,
            'filter' => $filter,
            'counts' => [
                'open' => (clone $base)->open()->count(),
                'overdue' => (clone $base)->overdue()->count(),
                'done' => (clone $base)->whereNotNull('completed_at')->count(),
            ],
            'showAll' => $assignee === null,
        ]);
    }
}
