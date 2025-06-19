<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'company_name' => $this->company_name ?? 'My Company',
            'address' => $this->address ?? 'My Address',
            'email' => $this->email ?? 'Wd2Tq@example.com',
            'phone' => $this->phone ?? 'XXXXXXXX',
            'min_quantity' => $this->min_quantity ?? 10,
        ];
    }
}

