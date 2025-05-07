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
        'niveau_actuel', // Si ajouté
        'service_id',
        // 'personnel_assigne_id', // Si ajouté
    ];

    /**
     * Relation: Un distributeur appartient à un Service.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Relation: Un distributeur peut être utilisé dans plusieurs transactions Carburant.
     */
    public function carburants(): HasMany
    {
        return $this->hasMany(Carburant::class);
    }

    // /**
    //  * Relation: Un distributeur peut avoir un Personnel principal assigné.
    //  */
    // public function personnelAssigne(): BelongsTo
    // {
    //     return $this->belongsTo(Personnel::class, 'personnel_assigne_id');
    // }
}