<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserssExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::all();
    }
    public function headings(): array
    {
        return [

            'Name',
            'Email',
            'Created At',
            'Updated At',
        ];
    }
    public function map($user): array
    {
        return [

            $user->name,
            $user->email,
            $user->created_at,
            $user->updated_at,  
        ];
    }
}
