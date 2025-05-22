<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Important

class Personnel extends Authenticatable
{
    use HasFactory;

    // soute_id ne doit PAS être ici
    protected $fillable = [
        'nom', 'prenom', 'matricule', 'email', 'corps_arme_id', 'service_id'
    ];

    public function corpsArme(): BelongsTo
    {
        return $this->belongsTo(CorpsArme::class); // Assurez-vous que le modèle CorpsArme existe
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class); // Assurez-vous que le modèle Service existe
    }

    // Relation Many-to-Many
    public function soutes(): BelongsToMany
    {
        return $this->belongsToMany(Soute::class, 'personnel_soute') // Nom de la table pivot
                     ->withTimestamps(); // Si vous avez des timestamps sur la table pivot et voulez y accéder
    }

    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }
    public function distributions()
{
    return $this->hasMany(Distribution::class);
}
public function depotages(): HasMany
{
    return $this->hasMany(Depotage::class);
}
}