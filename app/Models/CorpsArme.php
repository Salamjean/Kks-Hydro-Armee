<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model; // Supprimer ou commenter cette ligne
use Illuminate\Foundation\Auth\User as Authenticatable; // Utiliser Authenticatable
use Illuminate\Notifications\Notifiable; // Si vous utilisez les notifications Laravel
use Laravel\Sanctum\HasApiTokens; // Si vous prévoyez d'utiliser Sanctum pour les API

// Remplacer 'extends Model' par 'extends Authenticatable'
class CorpsArme extends Authenticatable
{
    // Ajouter le trait Notifiable si vous envoyez des notifications au modèle CorpsArme
    use Notifiable;
    // Ajouter HasApiTokens si nécessaire
    // use HasApiTokens, Notifiable;

    /**
     * Le nom de la table associée au modèle.
     * Laravel essaie de deviner 'corps_armes', mais c'est bien de le spécifier.
     * @var string
     */
    protected $table = 'corps_armes'; // Assurez-vous que c'est le bon nom de table

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'localisation',
        'password',
        // 'profile_picture', // Ajoutez si vous avez ce champ
    ];

    /**
     * Les attributs qui doivent être cachés pour les sérialisations.
     * Important pour la sécurité, notamment le mot de passe.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token', // Laravel utilise ce champ
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime', // Si vous ajoutez la vérification d'email Laravel
        'password' => 'hashed', // Indique à Laravel que ce champ est hashé (depuis L10+)
                                 // Si version < L10, le hashing est géré manuellement comme vous le faites déjà
    ];
}