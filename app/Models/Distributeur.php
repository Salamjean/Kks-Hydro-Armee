<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Distributeur extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifiant',
        'type',
        'capacite',
        'niveau_actuel',
        'soute_id', // <<--- MODIFIÉ (anciennement service_id)
    ];

    /**
     * Relation: Un distributeur appartient à une Soute.
     */
    public function soute(): BelongsTo // <<--- MODIFIÉ
    {
        return $this->belongsTo(Soute::class);
    }

    public function carburants(): HasMany
    {
        return $this->hasMany(Carburant::class);
    }
}