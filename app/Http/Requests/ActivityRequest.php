<?php

namespace App\Http\Requests;

use App\Enums\ActivityType;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivityRequest extends FormRequest
{
    public const SUBJECT_TYPES = ['company', 'contact', 'deal'];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isTask = $this->input('type') === ActivityType::Task->value;
        $table = in_array($this->input('subject_type'), self::SUBJECT_TYPES, true)
            ? (new (Relation::getMorphedModel($this->input('subject_type'))))->getTable()
            : 'companies';

        return [
            'subject_type' => ['required', Rule::in(self::SUBJECT_TYPES)],
            'subject_id' => ['required', 'integer', Rule::exists($table, 'id')->whereNull('deleted_at')],
            'type' => ['required', Rule::enum(ActivityType::class)],
            'title' => [Rule::requiredIf($isTask), 'nullable', 'string', 'max:255'],
            'body' => [Rule::requiredIf(! $isTask), 'nullable', 'string', 'max:5000'],
            'due_at' => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => __('title'),
            'body' => __('note'),
            'due_at' => __('due date'),
        ];
    }
}
