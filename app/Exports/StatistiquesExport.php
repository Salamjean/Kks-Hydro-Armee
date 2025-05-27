<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

class StatistiquesExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $statistiques;

    public function __construct(Collection $statistiques)
    {
        $this->statistiques = $statistiques;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Nous allons mapper les objets pour s'assurer que les données sont des tableaux simples
        return $this->statistiques->map(function ($item) {
            return [
                'mois' => $item->mois,
                'distribution' => $item->distribution,
                'depotage' => $item->depotage,
            ];
        });
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Mois',
            'Distribution (L)',
            'Dépotage (L)',
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $cellRange = 'A1:W1'; // Tous les en-têtes
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
                // Vous pouvez ajouter plus de styles ici si nécessaire
            },
        ];
    }
}