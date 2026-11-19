<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Company;
use App\Models\Deal;

it('logs a note on a deal', function () {
    $user = salesRep();
    $deal = Deal::factory()->create();

    $this->actingAs($user)->post('/activities', [
        'subject_type' => 'deal', 'subject_id' => $deal->id, 'type' => 'call', 'body' => 'Discussed the budget.',
    ])->assertRedirect();

    $activity = $deal->activities()->first();

    expect($activity->type)->toBe(ActivityType::Call)
        ->and($activity->user_id)->toBe($user->id)
        ->and($activity->assigned_to)->toBeNull();
});

it('creates a task assigned to a colleague', function () {
    $colleague = salesRep('Colleague');
    $company = Company::factory()->create();

    $this->actingAs(salesRep())->post('/activities', [
        'subject_type' => 'company', 'subject_id' => $company->id, 'type' => 'task',
        'title' => 'Send the proposal', 'due_at' => now()->addDay()->toDateTimeString(), 'assigned_to' => $colleague->id,
    ])->assertSessionHasNoErrors();

    $this->actingAs($colleague)->get('/tasks')->assertSee('Send the proposal');
});

it('validates the subject and requires a title for tasks', function () {
    $this->actingAs(salesRep())->post('/activities', ['subject_type' => 'user', 'subject_id' => 1, 'type' => 'note', 'body' => 'x'])
        ->assertSessionHasErrors('subject_type');

    $this->actingAs(salesRep())->post('/activities', ['subject_type' => 'company', 'subject_id' => Company::factory()->create()->id, 'type' => 'task'])
        ->assertSessionHasErrors('title');
});

it('toggles a task via JSON, only for people involved', function () {
    $assignee = salesRep();
    $task = Activity::factory()->task($assignee)->create();

    $this->actingAs(salesRep('Stranger'))->patchJson("/activities/{$task->id}/toggle")->assertForbidden();

    $this->actingAs($assignee)->patchJson("/activities/{$task->id}/toggle")->assertOk()->assertJson(['completed' => true]);
    expect($task->fresh()->completed_at)->not->toBeNull();
});

it('shows overdue tasks in their own tab', function () {
    $user = salesRep();
    Activity::factory()->task($user, now()->subDays(2))->create(['title' => 'Late follow-up']);
    Activity::factory()->task($user, now()->addDays(3))->create(['title' => 'Future call']);

    $this->actingAs($user)->get('/tasks?filter=overdue')->assertSee('Late follow-up')->assertDontSee('Future call');
});
