<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class User
 * @property integer $id
 * @property string $role
 * @property string $email
 * @property string $first_name
 * @property string $last_name
 * @property string $password
 * @property $last_login_at
 * @property $email_verified_at
 * @property $created_at
 * @property $updated_at
 * @property Exercise[] $exercises
 * @property Workout[] $workouts
 * @package App\Models
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable, HasFactory;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        'email', 'first_name', 'last_name', 'password'
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        'password',
    ];

    /**
     * @inheritdoc
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @inheritdoc
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * Get all user's created exercises.
     */
    public function exercises(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Exercise::class, 'author_id');
    }

    /**
     * Get all user's created workouts.
     */
    public function workouts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Workout::class, 'author_id');
    }
}
