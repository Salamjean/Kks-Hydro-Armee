<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'personnel_id',
        'soute_id',
        'nom_chauffeur',
        'immatriculation_vehicule',
        'type_carburant',
        'quantite',
        'date_depotage',
        'heure_depotage',
    ];

    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }

    public function soute()
    {
        return $this->belongsTo(Soute::class);
    }
}