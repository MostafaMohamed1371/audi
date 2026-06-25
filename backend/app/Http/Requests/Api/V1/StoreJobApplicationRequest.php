<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobApplicationRequest extends FormRequest
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
            'jobOpeningId' => ['nullable', 'integer', 'exists:job_openings,id'],
            'fullName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'coverLetter' => ['nullable', 'string', 'max:5000'],
            'cvUrl' => ['nullable', 'string', 'max:500'],
        ];
    }
}
