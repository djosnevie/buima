<?php

namespace App\Imports;

use App\Models\Categorie;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class CategoriesImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $etablissementId = auth()->user()->etablissement_id;
        
        foreach ($rows as $row) {
            $nom = $row['categorie'] ?? null;
            if (!$nom) continue;

            Categorie::firstOrCreate(
                [
                    'nom' => $nom, 
                    'etablissement_id' => $etablissementId
                ],
                [
                    'type' => 'plat', 
                    'couleur' => '#3B82F6',
                    'ordre' => (Categorie::where('etablissement_id', $etablissementId)->max('ordre') ?? 0) + 1,
                ]
            );
        }
    }
}
