<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Admin;

use App\Enums\MembershipApplicationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMembershipApplicationStatusRequest extends FormRequest
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
            'status' => ['required', Rule::enum(MembershipApplicationStatus::class)],
        ];
    }
}
