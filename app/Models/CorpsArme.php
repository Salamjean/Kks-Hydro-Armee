<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany; // Importer HasMany

class CorpsArme extends Authenticatable
{
    use Notifiable;

    protected $table = 'corps_armes';

    protected $fillable = [
        'name', // 'name' au lieu de 'nom' ? A vérifier
        'email',
        'localisation',
        'password',
        //'profile_picture',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relation: Un CorpsArme peut avoir plusieurs Services.
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

     /**
      * Relation: Un CorpsArme peut avoir plusieurs Personnels.
      */
    public function personnels(): HasMany
    {
        return $this->hasMany(Personnel::class);
    }

     /**
      * Relation: Un CorpsArme peut avoir plusieurs transactions Carburant associées.
      */
    public function carburants(): HasMany
    {
        return $this->hasMany(Carburant::class);
    }
}