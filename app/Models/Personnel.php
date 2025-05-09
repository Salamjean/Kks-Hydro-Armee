<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class Personnel extends Authenticatable // <<--- MODIFIÉ
{
    use HasFactory, Notifiable; // <<--- MODIFIÉ (ajout Notifiable si utilisé)

    protected $table = 'personnels';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'matricule',
        'password', // <<--- AJOUTÉ
        'corps_arme_id',
        'service_id',
        'soute_id',
    ];
     /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token', // <<--- AJOUTÉ
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime', // Si tu implémentes la vérification d'email
        'password' => 'hashed',          // <<--- AJOUTÉ (pour Laravel 10+)
                                         // Si version < L10, le hashing se fera manuellement
    ];

    public function corpsArme(): BelongsTo
    {
        return $this->belongsTo(CorpsArme::class);
    }

    /**
     * Relation: Un personnel appartient à une Soute (optionnel).
     */
    public function soute(): BelongsTo // <<--- NOUVELLE RELATION
    {
        return $this->belongsTo(Soute::class);
    }

    /**
     * Relation: Un personnel appartient à un Service (optionnel).
     * On le garde pour l'instant, tu décideras plus tard si tu en as besoin.
     * Si Soute remplace Service pour l'organisation, cette relation pourrait disparaître.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function carburants(): HasMany
    {
        return $this->hasMany(Carburant::class);
    }

    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }
    
}