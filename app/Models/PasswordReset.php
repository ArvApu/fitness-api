<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

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
    use HasFactory;

    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        'email', 'token', 'expires_at',
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        'token',
    ];
}
