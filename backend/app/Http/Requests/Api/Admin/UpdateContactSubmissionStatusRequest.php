<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Admin;

use App\Enums\ContactSubmissionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContactSubmissionStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(ContactSubmissionStatus::class)],
        ];
    }
}
