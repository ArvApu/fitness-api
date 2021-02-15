<?php

namespace App\Models;

/**
 * Class PasswordReset
 * @property integer $id
 * @property string $email
 * @property string $token
 * @property $expires_at
 * @package App\Models
 */
class PasswordReset extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'token', 'expires_at',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'token',
    ];

    public $timestamps = false;
}
