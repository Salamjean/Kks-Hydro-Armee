<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Carburant extends Model
{
    use HasFactory;

    protected $table = 'carburants'; // Nom de la table

    protected $fillable = [
        'type_carburant',
        'quantite',
        'vehicule_receveur_immat',
        'kilometrage_receveur',
        'date_transaction',
        'corps_arme_id',
        'personnel_id',
        'distributeur_id',
        'notes',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array
     */
    protected $casts = [
        'date_transaction' => 'datetime',
        'quantite' => 'decimal:2',
    ];

    /**
     * Relation: Une transaction appartient à un CorpsArme.
     */
    public function corpsArme(): BelongsTo
    {
        return $this->belongsTo(CorpsArme::class);
    }

    /**
     * Relation: Une transaction a été effectuée par un Personnel.
     */
    public function personnel(): BelongsTo
    {
        return $this->belongsTo(Personnel::class);
    }

    /**
     * Relation: Une transaction a utilisé un Distributeur.
     */
    public function distributeur(): BelongsTo
    {
        return $this->belongsTo(Distributeur::class);
    }
}