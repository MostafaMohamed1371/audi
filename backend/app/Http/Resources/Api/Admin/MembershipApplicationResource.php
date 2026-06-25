<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\MembershipApplication */
class MembershipApplicationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'organizationName' => $this->organization_name,
            'contactName' => $this->contact_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'countryCode' => $this->country_code,
            'city' => $this->city,
            'message' => $this->message,
            'status' => $this->status instanceof \BackedEnum ? $this->status->value : $this->status,
            'createdAt' => $this->created_at?->toIso8601String(),
            'updatedAt' => $this->updated_at?->toIso8601String(),
        ];
    }
}
