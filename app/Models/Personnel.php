<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Personnel extends Model
{
    use HasFactory;

    protected $table = 'personnels'; // Spécifier si différent de 'personnel'

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'matricule',
        'corps_arme_id',
        'service_id',
        // 'distributeur_id', // Si ajouté dans la migration
    ];

    /**
     * Relation: Un personnel appartient à un CorpsArme.
     */
    public function corpsArme(): BelongsTo
    {
        return $this->belongsTo(CorpsArme::class);
    }

    /**
     * Relation: Un personnel appartient à un Service (optionnel).
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Relation: Un personnel peut avoir effectué plusieurs transactions Carburant.
     */
    public function carburants(): HasMany
    {
        return $this->hasMany(Carburant::class);
    }

    // /**
    //  * Relation: Un personnel peut être assigné à un Distributeur (si ajouté).
    //  */
    // public function distributeur(): BelongsTo
    // {
    //     return $this->belongsTo(Distributeur::class);
    // }

    /**
     * Accesseur pour obtenir le nom complet.
     */
    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }
}