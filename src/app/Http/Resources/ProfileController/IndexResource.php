<?php

namespace App\Http\Resources\ProfileController;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexResource extends JsonResource
{
    public static $wrap = 'user';
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => [
                'id' => $this->role?->id,
                'name' => $this->role?->name,
                'slug' => $this->role?->slug
            ],
            'phone' => $this->phone,
            'company' => $this->company->load('logo', 'documents', 'region'),
            'subscribe_end' => $this->subscribe_end,
            'subscribe_status' => $this->subscribe_status()
        ];
    }
}
