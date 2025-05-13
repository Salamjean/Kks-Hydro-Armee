<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Soute extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'matricule_soute',
        'localisation',
        'corps_arme_id',
        'type_carburant_principal',
        'capacite_totale',
        'niveau_actuel_global',
        'description',
    ];

    protected $casts = [
        'capacite_totale' => 'decimal:2',
        'niveau_actuel_global' => 'decimal:2',
    ];

    public function corpsArme(): BelongsTo
    {
        return $this->belongsTo(CorpsArme::class);
    }

    public function personnels(): BelongsToMany
    {
        return $this->belongsToMany(Personnel::class, 'personnel_soute')
                    ->withTimestamps();
    }

    public function distributeurs(): HasMany // Une soute peut avoir plusieurs pompes/distributeurs
    {
        return $this->hasMany(Distributeur::class);
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($soute) {
            if (empty($soute->matricule_soute)) {
                // Génère un matricule unique. Tu peux adapter la logique de génération.
                // Exemple: SOUTE-CORPID-RANDOM
                $prefix = "SOUTE";
                $corpsId = $soute->corps_arme_id ?? 'X'; // Prend l'ID du corps si disponible
                $randomPart = strtoupper(Str::random(6)); // 6 caractères aléatoires
                $soute->matricule_soute = $prefix . '-' . $corpsId . '-' . $randomPart;

                // Assurer l'unicité (boucle simple, pour des cas rares de collision)
                while (static::where('matricule_soute', $soute->matricule_soute)->exists()) {
                    $randomPart = strtoupper(Str::random(6));
                    $soute->matricule_soute = $prefix . '-' . $corpsId . '-' . $randomPart;
                }
            }
        });
    }
    // RELATION MANY-TO-MANY AVEC PERSONNEL
  
}