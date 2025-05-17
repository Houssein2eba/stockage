<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return   [  'id' => $this->id,
        'name' => $this->name,
        'email' => $this->email,
        'number' => $this->number,
        'role' => $this->whenLoaded('roles', function () {
            $role = $this->roles->first();
            return $role ? [
                'id' => $role->id,
                'name' => $role->name
            ] : null;
        }),
    ];
    }
}
