<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberCityStatsRequest extends FormRequest
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
            'items' => ['required', 'array', 'min:1'],
            'items.*.key' => ['required', 'string', 'in:countries,cities,members'],
            'items.*.value' => ['nullable', 'integer', 'min:0'],
            'items.*.autoCalculate' => ['sometimes', 'boolean'],
            'items.*.auto_calculate' => ['sometimes', 'boolean'],
            'items.*.label' => ['sometimes', 'array'],
            'items.*.label.ar' => ['sometimes', 'string', 'max:255'],
            'items.*.label.en' => ['sometimes', 'string', 'max:255'],
            'items.*.unit' => ['sometimes', 'array'],
            'items.*.unit.ar' => ['sometimes', 'string', 'max:255'],
            'items.*.unit.en' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
