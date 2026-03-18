<?php

namespace App\Exports;

use App\Models\Categorie;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CategoriesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        $user = auth()->user();
        $query = Categorie::query();

        if ($user->isManager()) {
            $query->whereIn('etablissement_id', $user->getAccessibleEtablissementIds());
        } else {
            $query->where('etablissement_id', $user->etablissement_id);
        }

        return $query->latest()->get();
    }

    public function map($categorie): array
    {
        return [
            $categorie->nom,
        ];
    }

    public function headings(): array
    {
        return [
            'Catégorie',
        ];
    }
}
