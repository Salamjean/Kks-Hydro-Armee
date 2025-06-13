<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Important
use Illuminate\Support\Str;

class Soute extends Model
{
    use HasFactory;

    // personnel_id ne doit PAS être ici
    protected $fillable = [
        'nom',
        'matricule_soute',
        'localisation',
        'corps_arme_id',
        'types_carburants_stockes',
        'capacite_diesel',
        'capacite_kerozen',
        'capacite_essence',
        'niveau_actuel_diesel',
        'niveau_actuel_kerozen',
        'niveau_actuel_essence',
        'seuil_alert_diesel', // Ajouté
    'seuil_alert_kerozen', // Ajouté
    'seuil_alert_essence', // Ajouté
    'seuil_indisponibilite_diesel',
    'seuil_indisponibilite_kerozen',
    'seuil_indisponibilite_essence',
        'description',
    ];

    protected $casts = [
        'types_carburants_stockes' => 'array',
        'capacite_diesel' => 'decimal:2',
        'capacite_kerozen' => 'decimal:2',
        'capacite_essence' => 'decimal:2',
        'niveau_actuel_diesel' => 'decimal:2',
        'niveau_actuel_kerozen' => 'decimal:2',
        'niveau_actuel_essence' => 'decimal:2',
        'seuil_alert_diesel' => 'decimal:2', // Ajouté
        'seuil_alert_kerozen' => 'decimal:2', // Ajouté
        'seuil_alert_essence' => 'decimal:2', // Ajouté
        'seuil_indisponibilite_diesel' => 'decimal:2',
        'seuil_indisponibilite_kerozen' => 'decimal:2',
        'seuil_indisponibilite_essence' => 'decimal:2',
    ];

    public function distributeurs(): HasMany
    {
        return $this->hasMany(Distributeur::class); // Assurez-vous que le modèle Distributeur existe
    }

    public function corpsArme(): BelongsTo
    {
        return $this->belongsTo(CorpsArme::class); // Assurez-vous que le modèle CorpsArme existe
    }
    public function distributions()
{
    return $this->hasMany(Distribution::class);
}

    // Relation Many-to-Many (inverse de celle dans Personnel)
    public function personnels(): BelongsToMany
    {
        return $this->belongsToMany(Personnel::class, 'personnel_soute') // Nom de la table pivot
                     ->withTimestamps();
    }
    public function depotages(): HasMany
    {
        return $this->hasMany(Depotage::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($soute) {
            if (empty($soute->matricule_soute)) {
                $prefix = "SOUTE";
                // Assurez-vous que $soute->corps_arme_id est disponible au moment de la création
                // ou que vous avez une logique pour le gérer s'il est null.
                $corpsId = $soute->corps_arme_id ?? 'X';
                $randomPart = strtoupper(Str::random(6));
                $soute->matricule_soute = $prefix . '-' . $corpsId . '-' . $randomPart;

                while (static::where('matricule_soute', $soute->matricule_soute)->exists()) {
                    $randomPart = strtoupper(Str::random(6));
                    $soute->matricule_soute = $prefix . '-' . $corpsId . '-' . $randomPart;
                }
            }
        });
    }
    // Vérifie si le niveau d'un carburant a atteint son seuil d'alerte.
    // CECI EST JUSTE UNE INFORMATION, ÇA NE BLOQUE RIEN.
    
    public function estIndisponible($typeCarburant)
    {
        $typeCarburant = strtolower($typeCarburant);
        $niveauActuel = $this->{"niveau_actuel_$typeCarburant"};
        $seuilIndisponibilite = $this->{"seuil_indisponibilite_$typeCarburant"};
        
        return !is_null($seuilIndisponibilite) && $niveauActuel <= $seuilIndisponibilite;
    }
    
    public function estEnAlerte($typeCarburant)
    {
        $typeCarburant = strtolower($typeCarburant);
        $niveauActuel = $this->{"niveau_actuel_$typeCarburant"};
        $seuilAlerte = $this->{"seuil_alert_$typeCarburant"};
        
        return !is_null($seuilAlerte) && $niveauActuel <= $seuilAlerte;
    }
    
    public function peutDistribuer($typeCarburant, $quantite)
    {
        $typeCarburant = strtolower($typeCarburant);
        
        // Vérification d'indisponibilité (bloque la distribution)
        if ($this->estIndisponible($typeCarburant)) {
            return false;
        }
        
        // Vérification du stock disponible
        $niveauActuel = $this->{"niveau_actuel_$typeCarburant"};
        return $niveauActuel >= $quantite;
    }
}
