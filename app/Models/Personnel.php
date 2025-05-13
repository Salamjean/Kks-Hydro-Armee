<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Personnel extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'matricule',
        'email',
        'corps_arme_id',
        'service_id',
        // 'Soute_id'
    ];

    public function corpsArme(): BelongsTo
    {
        return $this->belongsTo(CorpsArme::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    
public function soutes(): BelongsToMany
    {
        return $this->belongsToMany(Soute::class, 'personnel_soute')
                    ->withTimestamps();
    }
    // Accessor pour le nom complet
    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }
}