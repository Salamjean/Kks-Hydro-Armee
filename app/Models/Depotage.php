<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Depotage extends Model
{
    use HasFactory;

    protected $fillable = [
        'soute_id',
        'personnel_id',
        'date_depotage',
        'heure_depotage',
        'nom_operateur',
        'nom_societe_transporteur',
        'nom_chauffeur_transporteur',
        'immatriculation_vehicule_transporteur',
        'produit',
        'volume_transporte_l',
        'numero_bon_livraison',
        'niveau_avant_depotage_l',
        'volume_recu_l',
        'observations',
    ];

    protected $casts = [
        'date_depotage' => 'date',
        'volume_transporte_l' => 'decimal:2',
        'niveau_avant_depotage_l' => 'decimal:2',
        'volume_recu_l' => 'decimal:2',
    ];

    public function soute(): BelongsTo
    {
        return $this->belongsTo(Soute::class);
    }

    public function personnel(): BelongsTo // Le personnel (pompiste) qui a enregistré
    {
        return $this->belongsTo(Personnel::class);
    }
}