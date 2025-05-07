<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'localisation',
        'corps_arme_id',
    ];

    /**
     * Relation: Un service appartient à un CorpsArme.
     */
    public function corpsArme(): BelongsTo
    {
        return $this->belongsTo(CorpsArme::class);
    }

    /**
     * Relation: Un service peut avoir plusieurs Personnels.
     */
    public function personnels(): HasMany
    {
        return $this->hasMany(Personnel::class);
    }

    /**
     * Relation: Un service peut avoir plusieurs Distributeurs.
     */
    public function distributeurs(): HasMany
    {
        return $this->hasMany(Distributeur::class);
    }
}